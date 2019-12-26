<?php
/**
 * Freenom域名自动续期
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2018/8/9
 * @time 10:05
 */

error_reporting(E_ERROR);
ini_set('display_errors', 1);
set_time_limit(0);

define('IS_CLI', PHP_SAPI === 'cli' ? true : false);
define('DS', DIRECTORY_SEPARATOR);
define('VENDOR_PATH', realpath('vendor') . DS);

date_default_timezone_set('Asia/Shanghai');

/**
 * 自定义错误处理
 */
register_shutdown_function('customize_error_handler');
function customize_error_handler()
{
    if (!is_null($error = error_get_last())) {
        system_log($error);

        $response = [
            'STATUS' => 9,
            'MESSAGE_ARRAY' => array(
                array(
                    'MESSAGE' => '程序执行出错，请稍后再试。'
                )
            ),
            'SYSTEM_DATE' => date('Y-m-d H:i:s')
        ];

        header('Content-Type: application/json');

        echo json_encode($response);
    }
}

/**
 * 自定义异常处理
 * 设置默认的异常处理程序，用于没有用 try/catch 块来捕获的异常。 在 exception_handler 调用后异常会中止。由于要实现在 try/catch 块
 * 每次抛出异常后，在catch块自动发送一封通知邮件的功能，而使用PHPMailer发送邮件的流程本身也可能抛出异常，要在catch代码块中捕获异常就意味着
 * 要嵌套try/catch，而我并不想嵌套try/catch代码块，因此自定义此异常处理函数。
 */
set_exception_handler('exception_handler');
function exception_handler($e)
{
    system_log(sprintf('#%d - %s', $e->getCode(), $e->getMessage()));
}

/**
 * 记录程序日志
 * @param array|string $logContent 日志内容
 * @param string $mark LOG | ERROR | WARNING 日志标志
 */
function system_log($logContent, $mark = 'LOG')
{
    try {
        $logPath = __DIR__ . '/logs/' . date('Y') . '/' . date('m') . '/';
        $logFile = $logPath . date('d') . '.php';

        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
            chmod($logPath, 0777);
        }

        $handle = fopen($logFile, 'a'); // 文件不存在则自动创建

        if (!filesize($logFile)) {
            fwrite($handle, "<?php defined('VENDOR_PATH') or die('No direct script access allowed.'); ?>" . PHP_EOL . PHP_EOL);
            chmod($logFile, 0666);
        }

        fwrite($handle, $mark . ' - ' . date('Y-m-d H:i:s') . ' --> ' . (IS_CLI ? 'CLI' : 'URI: ' . $_SERVER['REQUEST_URI'] . PHP_EOL . 'REMOTE_ADDR: ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL . 'SERVER_ADDR: ' . $_SERVER['SERVER_ADDR']) . PHP_EOL . (is_string($logContent) ? $logContent : var_export($logContent, true)) . PHP_EOL); // CLI模式下，$_SERVER中几乎无可用值

        fclose($handle);
    } catch (\Exception $e) {
        // do nothing
    }
}

require VENDOR_PATH . 'autoload.php';
require 'llfexception.php';

use Curl\Curl;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class FREENOM
{
    // FREENOM登录地址
    const LOGIN_URL = 'https://my.freenom.com/dologin.php';

    // 域名状态地址
    const DOMAIN_STATUS_URL = 'https://my.freenom.com/domains.php';

    // 域名续期地址
    const RENEW_DOMAIN_URL = 'https://my.freenom.com/domains.php?submitrenewals=true';

    /**
     * @var FREENOM
     */
    protected static $instance;

    /**
     * @var array 配置文件
     */
    protected static $config;

    /**
     * @var int curl超时秒数
     */
    protected static $timeOut = 20;

    /**
     * @var string 储存用户登录状态的cookie
     */
    protected static $authCookie;

    /**
     * @var string 匹配token的正则
     */
    private static $tokenRegex = '/name="token" value="([^"]+)"/i';

    /**
     * @var string 匹配域名信息的正则
     */
    private static $domainInfoRegex = '/<tr><td>([^<]+)<\/td><td>([^<]+)<\/td><td>[^<]+<span class="([^"]+)">([^<]+)<\/span>[^&]+&domain=(\d+)"/i';

    /**
     * @var string 记录续期出错的域名，用于邮件通知内容
     */
    public $notRenewed = '';

    /**
     * @var string 续期成功的域名
     */
    public $renewed = '';

    /**
     * @var string 域名状态信息，用于邮件通知内容
     */
    public $domainsInfo = '';

    public function __construct()
    {
        static::$config = require __DIR__ . DS . 'config.php';
    }

    public static function instance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * 自动登录
     * @return mixed
     * @throws ErrorException
     * @throws \Exception
     */
    public function autoLogin()
    {
        $curl = new Curl();
        $curl->setUserAgent(static::$config['userInfo']['userAgent']);
        $curl->setReferrer('https://my.freenom.com/clientarea.php');
        $curl->setHeaders([
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        $curl->setTimeout(static::$timeOut);
        $curl->post(static::LOGIN_URL, [
            'username' => static::$config['userInfo']['username'],
            'password' => static::$config['userInfo']['password']
        ]);

        if ($curl->error) {
            throw new LlfException(6001, [$curl->errorCode, $curl->errorMessage]);
        }

        $curl->close();

        if (!isset($curl->responseCookies['WHMCSZH5eHTGhfvzP'])) { // freenom有几率出现未成功登录也写此cookie的情况，所以此处不能完全断定是否登录成功，这是freenom的锅。若未成功登录，会在后面匹配域名信息的时候抛出异常。
            throw new LlfException(6002);
        }

        static::$authCookie = $curl->responseCookies['WHMCSZH5eHTGhfvzP'];

        return $curl->responseCookies;
    }

    /**
     * 域名续期
     * @return array
     * @throws ErrorException
     * @throws \Exception
     */
    public function renewDomains()
    {
        $curl = new Curl();
        $curl->setUserAgent(static::$config['userInfo']['userAgent']);
        $curl->setTimeout(static::$timeOut);
        $curl->setCookies([ // 验证登录状态
            'WHMCSZH5eHTGhfvzP' => static::$authCookie
        ]);

        /**
         * 取得需要续期的域名id以及页面token
         */
        $curl->setHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8');
        $curl->setReferrer('https://my.freenom.com/clientarea.php');
        $curl->setOpts([
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true
        ]);
        $curl->get(self::DOMAIN_STATUS_URL, [
            'a' => 'renewals'
        ]);

        if ($curl->error) {
            throw new LlfException(6003, [$curl->errorCode, $curl->errorMessage]);
        }

        // 取得token
        if (!preg_match(self::$tokenRegex, $curl->response, $token)) {
            throw new LlfException(6004);
        }
        $token = $token[1];

        // 取得域名数据
        if (!preg_match_all(self::$domainInfoRegex, $curl->response, $domains, PREG_SET_ORDER)) { // PREG_SET_ORDER结果排序为$matches[0]包含第一次匹配得到的所有匹配(包含子组)， $matches[1]是包含第二次匹配到的所有匹配(包含子组)的数组，以此类推。
            throw new LlfException(6005);
        }

        /**
         * 域名续期
         */
        $renew_log = '';
        foreach ($domains as $domain) {
            if (intval($domain[4]) <= 14) { // 免费域名只允许在到期前14天内续期
                $curl->setReferrer('https://my.freenom.com/domains.php?a=renewdomain&domain=' . $domain[5]);
                $curl->setHeader('Content-Type', 'application/x-www-form-urlencoded');
                $curl->post(static::RENEW_DOMAIN_URL, [
                    'token' => $token,
                    'renewalid' => $domain[5], // 域名id
                    'renewalperiod[' . $domain[5] . ']' => '12M', // 续期一年
                    'paymentmethod' => 'credit', // 支付方式 - 信用卡
                ]);

                if ($curl->error) {
                    throw new LlfException(6006, [$domain[1], $curl->errorCode, $curl->errorMessage]);
                }

                sleep(1); // 防止操作过于频繁

                if (stripos($curl->rawResponse, 'Order Confirmation') === false) { // 续期失败
                    $renew_log .= $domain[1] . '续期失败' . "\n";
                    $this->notRenewed .= sprintf('<a href="http://%s/" rel="noopener" target="_blank">%s</a>', $domain[1], $domain[1]);
                } else {
                    $renew_log .= $domain[1] . '续期成功' . "\n";
                    $this->renewed .= sprintf('<a href="http://%s/" rel="noopener" target="_blank">%s</a>', $domain[1], $domain[1]);
                    continue;
                }
            }

            $this->domainsInfo .= sprintf('<a href="http://%s/" rel="noopener" target="_blank">%s</a>' . '还有<span style="font-weight: bold; font-size: 16px;">%d</span>天到期，', $domain[1], $domain[1], intval($domain[4]));
            $this->sendTelegram($domain[1].' left '.$domain[4]);
        }

        system_log($renew_log ?: sprintf("在%s这个时刻，并没有需要续期的域名，写这条日志是为了证明我确实执行了。今次取得的域名信息如是：\n%s", date('Y-m-d H:i:s'), var_export($domains, true)));
        if ($this->notRenewed || $this->renewed) {
		if (static::$config['telegram']['enable'] == 'true') {
			$this->sendTelegram(
		[
	                    $this->renewed ? '续期成功：' . $this->renewed . '<br>' : '',
	                    $this->notRenewed ? '续期出错：' . $this->notRenewed . '<br>' : '',
	                    $this->domainsInfo ?: '啊咧~没看到其它域名呢。'
	                ]
			);
		} else {
				$this->sendEmail(
	                '主人，我刚刚帮你续期域名啦~',
	                [
	                    $this->renewed ? '续期成功：' . $this->renewed . '<br>' : '',
	                    $this->notRenewed ? '续期出错：' . $this->notRenewed . '<br>' : '',
	                    $this->domainsInfo ?: '啊咧~没看到其它域名呢。'
	                ]
	            );
		}		
	}

        $curl->close();

        return $curl->response;
    }

    /**
     * 发送邮件
     * @param string $subject 标题
     * @param string|array $content 正文
     * @param string $to 收件人，选传
     * @param string $template 模板，选传
     * @throws \Exception
     */
    public function sendEmail($subject, $content, $to = '', $template = '')
    {
        $mail = new PHPMailer(true);

        // 邮件服务配置
        $mail->SMTPDebug = static::$config['mail']['debug']; // debug，正式环境应关闭 0：关闭 1：客户端信息 2：客户端和服务端信息
        $mail->isSMTP(); // 告诉PHPMailer使用SMTP
        $mail->Host = 'smtp.gmail.com'; // SMTP服务器
        $mail->SMTPAuth = true; // 启用SMTP身份验证
        $mail->Username = static::$config['mail']['username']; // 账号
        $mail->Password = static::$config['mail']['password']; // 密码
        $mail->SMTPSecure = 'tls'; // 将加密系统设置为使用 - ssl（不建议使用）或tls
        $mail->Port = 587; // 设置SMTP端口号 - tsl使用587端口，ssl使用465端口

        $mail->CharSet = 'UTF-8'; // 防止中文邮件乱码
        $mail->setLanguage('zh_cn', VENDOR_PATH . '/phpmailer/phpmailer/language/'); // 设置语言

        $mail->setFrom(static::$config['mail']['from'], 'im robot'); // 发件人
        $mail->addAddress($to ?: static::$config['mail']['to'], '罗叔叔'); // 添加收件人，参数2选填
        $mail->addReplyTo(static::$config['mail']['replyTo'], '罗飞飞'); // 备用回复地址，收到的回复的邮件将被发到此地址

        /**
         * 抄送和密送都是添加收件人，抄送方式下，被抄送者知道除被密送者外的所有的收件人，密送方式下，
         * 被密送者知道所有的被抄送者，但不知道其它的被密送者。
         * 抄送好比@，密送好比私信。
         */
//        $mail->addCC('cc@example.com'); // 抄送
//        $mail->addBCC('bcc@example.com'); // 密送

        // 添加附件，参数2选填
//        $mail->addAttachment('README.md', '说明.txt');

        // 内容
        $mail->Subject = $subject; // 标题
        /**
         * 正文
         * 使用html文件内容作为正文，其中的图片将被base64编码，另确保html样式为内联形式，且某些样式可能需要!important方能正常显示，
         * msgHTML方法的第二个参数指定html内容中图片的路径，在转换时会拼接html中图片的相对路径得到完整的路径，最右侧无需“/”，PHPMailer
         * 源码里有加。css中的背景图片不会被转换，这是PHPMailer已知问题，建议外链。
         * 此处也可替换为：
         * $mail->isHTML(true); // 设为html格式
         * $mail->Body = '正文'; // 支持html
         * $mail->AltBody = 'This is an HTML-only message. To view it, activate HTML in your email application.'; // 纯文本消息正文。不支持html预览的邮件客户端将显示此预览消息，其它情况将显示正常的body
         */
        $template = file_get_contents('mail/' . ($template ?: 'default') . '.html');
        if (is_array($content)) {
            array_unshift($content, $template);
            $message = call_user_func_array('sprintf', $content);
        } else if (is_string($content)) {
            $message = sprintf($template, $content);
        } else {
            throw new LlfException(6007);
        }
        $mail->msgHTML($message, __DIR__ . '/mail');

        if (!$mail->send()) throw new \Exception($mail->ErrorInfo);
    }
    
    public function sendTelegram($text)
    {
            $token = static::$config['telegram']['token'];
            $chatid = static::$config['telegram']['chatid'];
            $curl = new Curl();
            if (!is_string($text)) {
                    $text=var_export($text,true);
            }
            $curl->get('https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$chatid.'&text='.$text);
            $curl->close();
    }
}

try {
    /**
     * 先登录
     */
    FREENOM::instance()->autoLogin();

    /**
     * 再续期
     */
    FREENOM::instance()->renewDomains();
} catch (LlfException $e) {
    system_log($e->getMessage());
if (static::$config['telegram']['enable'] == 'true') {
	FREENOM::instance()->sendTelegram(
		[$e->getMessage(),$e->getFile(),$e->getLine(),$e->getMessage()]
	);
} else {
	    FREENOM::instance()->sendEmail(
	        '主人，' . $e->getMessage(),
	        sprintf('具体是在%s文件的%d行，抛出了一个异常。异常的内容是%s，快去看看吧。', $e->getFile(), $e->getLine(), $e->getMessage()),
	        '',
	        'llfexception'
	    );
	}
} catch (\Exception $e) {
    system_log(sprintf('#%d - %s', $e->getCode(), $e->getMessage()));
}
