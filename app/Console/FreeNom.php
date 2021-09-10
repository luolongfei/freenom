<?php
/**
 * FreeNom域名自动续期
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2020/1/19
 * @time 17:29
 * @link https://github.com/luolongfei/freenom
 */

namespace Luolongfei\App\Console;

use Luolongfei\App\Exceptions\LlfException;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Luolongfei\Lib\Log;
use Luolongfei\Lib\Mail;
use Luolongfei\Lib\TelegramBot;

class FreeNom
{
    const VERSION = 'v0.3';

    const TIMEOUT = 34.52;

    // FreeNom登录地址
    const LOGIN_URL = 'https://my.freenom.com/dologin.php';

    // 域名状态地址
    const DOMAIN_STATUS_URL = 'https://my.freenom.com/domains.php?a=renewals';

    // 域名续期地址
    const RENEW_DOMAIN_URL = 'https://my.freenom.com/domains.php?submitrenewals=true';

    // 匹配token的正则
    const TOKEN_REGEX = '/name="token"\svalue="(?P<token>[^"]+)"/i';

    // 匹配域名信息的正则
    const DOMAIN_INFO_REGEX = '/<tr><td>(?P<domain>[^<]+)<\/td><td>[^<]+<\/td><td>[^<]+<span class="[^"]+">(?P<days>\d+)[^&]+&domain=(?P<id>\d+)"/i';

    // 匹配登录状态的正则
    const LOGIN_STATUS_REGEX = '/<li.*?Logout.*?<\/li>/i';

    /**
     * @var FreeNom
     */
    protected static $instance;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var CookieJar | bool
     */
    protected $jar = true;

    /**
     * @var string freenom账户
     */
    protected $username;

    /**
     * @var string freenom密码
     */
    protected $password;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'Accept-Encoding' => 'gzip, deflate, br',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36',
            ],
            'timeout' => self::TIMEOUT,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            'verify' => config('verifySSL'),
            'debug' => config('debug')
        ]);

        system_log(sprintf('当前程序版本 %s', self::VERSION));
    }

    /**
     * @return FreeNom
     */
    public static function instance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 登录
     */
    protected function login()
    {
        $this->client->post(self::LOGIN_URL, [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Referer' => 'https://my.freenom.com/clientarea.php'
            ],
            'form_params' => [
                'username' => $this->username,
                'password' => $this->password
            ],
            'cookies' => $this->jar
        ]);
    }

    /**
     * 续期
     *
     * @throws \Exception
     * @throws LlfException
     */
    public function renewDomains()
    {
        // 所有请求共用一个CookieJar实例
        $this->jar = new CookieJar();

        $this->login();
        $authCookie = $this->jar->getCookieByName('WHMCSZH5eHTGhfvzP')->getValue();
        if (empty($authCookie)) {
            throw new LlfException(34520002);
        }

        // 检查域名状态
        $response = $this->client->get(self::DOMAIN_STATUS_URL, [
            'headers' => [
                'Referer' => 'https://my.freenom.com/clientarea.php'
            ],
            'cookies' => $this->jar
        ]);
        $body = (string)$response->getBody();

        if (!preg_match(self::LOGIN_STATUS_REGEX, $body)) {
            throw new LlfException(34520009);
        }

        // 域名数据
        if (!preg_match_all(self::DOMAIN_INFO_REGEX, $body, $domains, PREG_SET_ORDER)) {
            throw new LlfException(34520003);
        }

        // 页面token
        if (!preg_match(self::TOKEN_REGEX, $body, $matches)) {
            throw new LlfException(34520004);
        }
        $token = $matches['token'];

        // 续期
        $result = '';
        $renewed = $renewedTG = ''; // 续期成功的域名
        $notRenewed = $notRenewedTG = ''; // 记录续期出错的域名，用于邮件通知内容
        $domainInfo = $domainInfoTG = ''; // 域名状态信息，用于邮件通知内容
        foreach ($domains as $d) {
            $domain = $d['domain'];
            $days = intval($d['days']);
            $id = $d['id'];

            // 免费域名只允许在到期前14天内续期
            if ($days <= 14) {
                try {
                    $response = $this->client->post(self::RENEW_DOMAIN_URL, [
                        'headers' => [
                            'Referer' => sprintf('https://my.freenom.com/domains.php?a=renewdomain&domain=%s', $id),
                            'Content-Type' => 'application/x-www-form-urlencoded'
                        ],
                        'form_params' => [
                            'token' => $token,
                            'renewalid' => $id,
                            sprintf('renewalperiod[%s]', $id) => '12M', // 续期一年
                            'paymentmethod' => 'credit', // 支付方式：信用卡
                        ],
                        'cookies' => $this->jar
                    ]);
                } catch (\Exception $e) {
                    system_log(sprintf('%s：续期请求出错：%s', $this->username, $e->getMessage()));
                    continue;
                }

                $body = (string)$response->getBody();
                sleep(1);

                if (stripos($body, 'Order Confirmation') === false) { // 续期失败
                    $result .= sprintf("%s续期失败\n", $domain);
                    $notRenewed .= sprintf('<a href="http://%s" rel="noopener" target="_blank">%s</a>', $domain, $domain);
                    $notRenewedTG .= sprintf('[%s](http://%s)  ', $domain, $domain);
                } else {
                    $result .= sprintf("%s续期成功\n", $domain);
                    $renewed .= sprintf('<a href="http://%s" rel="noopener" target="_blank">%s</a>', $domain, $domain);
                    $renewedTG .= sprintf('[%s](http://%s)  ', $domain, $domain);
                    continue;
                }
            }

            $domainInfo .= sprintf('<a href="http://%s" rel="noopener" target="_blank">%s</a>还有<span style="font-weight: bold; font-size: 16px;">%d</span>天到期，', $domain, $domain, $days);
            $domainInfoTG .= sprintf('[%s](http://%s)还有*%d*天到期，', $domain, $domain, $days);
        }
        $domainInfoTG .= "更多信息可以参考[Freenom官网](https://my.freenom.com/domains.php?a=renewals)哦~\n\n（如果你不想每次执行都收到推送，请将 .env 中 NOTICE_FREQ 的值设为0，使程序只在有续期操作时才推送）";

        if ($notRenewed || $renewed) {
            Mail::send(
                '主人，我刚刚帮你续期域名啦~',
                [
                    $this->username,
                    $renewed ? sprintf('续期成功：%s<br>', $renewed) : '',
                    $notRenewed ? sprintf('续期出错：%s<br>', $notRenewed) : '',
                    $domainInfo ?: '哦豁，没看到其它域名。'
                ]
            );
            TelegramBot::send(sprintf(
                "主人，我刚刚帮你续期域名啦~\n\n%s%s\n另外，%s",
                $renewedTG ? sprintf("续期成功：%s\n", $renewedTG) : '',
                $notRenewedTG ? sprintf("续期失败：%s\n", $notRenewedTG) : '',
                $domainInfoTG
            ));
            system_log(sprintf("%s：续期结果如下：\n%s", $this->username, $result));
        } else {
            if (config('noticeFreq') == 1) {
                Mail::send(
                    '报告，今天没有域名需要续期',
                    [
                        $this->username,
                        $domainInfo
                    ],
                    '',
                    'notice'
                );
                TelegramBot::send("报告，今天没有域名需要续期，所有域名情况如下：\n\n" . $domainInfoTG);
            } else {
                system_log('当前通知频率为「仅当有续期操作时」，故本次不会推送通知');
            }
            system_log(sprintf('%s：<green>执行成功，今次没有需要续期的域名</green>', $this->username));
        }
    }

    /**
     * 二维数组去重
     *
     * @param array $array 原始数组
     * @param array $keys 可指定对应的键联合
     *
     * @return bool
     */
    public function arrayUnique(array &$array, array $keys = [])
    {
        if (!isset($array[0]) || !is_array($array[0])) {
            return false;
        }

        if (empty($keys)) {
            $keys = array_keys($array[0]);
        }

        $tmp = [];
        foreach ($array as $k => $items) {
            $combinedKey = '';
            foreach ($keys as $key) {
                $combinedKey .= $items[$key];
            }

            if (isset($tmp[$combinedKey])) {
                unset($array[$k]);
            } else {
                $tmp[$combinedKey] = $k;
            }
        }
        unset($tmp);

        return true;
    }

    /**
     * 获取freenom账户信息
     *
     * @return array
     * @throws LlfException
     */
    protected function getAccounts()
    {
        $accounts = [];
        $multipleAccounts = preg_replace('/\s/', '', env('MULTIPLE_ACCOUNTS'));
        if (preg_match_all('/<(?P<u>.*?)>@<(?P<p>.*?)>/i', $multipleAccounts, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $accounts[] = [
                    'username' => $m['u'],
                    'password' => $m['p']
                ];
            }
        }

        $username = env('FREENOM_USERNAME');
        $password = env('FREENOM_PASSWORD');
        if ($username && $password) {
            $accounts[] = [
                'username' => $username,
                'password' => $password
            ];
        }

        if (empty($accounts)) {
            throw new LlfException(34520001);
        }

        // 去重
        $this->arrayUnique($accounts);

        return $accounts;
    }

    /**
     * 发送异常报告
     *
     * @param \Exception $e
     *
     * @throws \Exception
     */
    private function sendExceptionReport($e)
    {
        Mail::send(
            '主人，' . $e->getMessage(),
            [
                $this->username,
                sprintf('具体是在%s文件的第%d行，抛出了一个异常。异常的内容是%s，快去看看吧。', $e->getFile(), $e->getLine(), $e->getMessage()),
            ],
            '',
            'LlfException'
        );

        TelegramBot::send(sprintf(
            '主人，出错了。具体是在%s文件的第%d行，抛出了一个异常。异常的内容是%s，快去看看吧。（账户：%s）',
            $e->getFile(),
            $e->getLine(),
            $e->getMessage(),
            $this->username
        ), '', false);
    }

    /**
     * @throws LlfException
     * @throws \Exception
     */
    public function handle()
    {
        $accounts = $this->getAccounts();
        foreach ($accounts as $account) {
            try {
                $this->username = $account['username'];
                $this->password = $account['password'];

                $this->renewDomains();
            } catch (LlfException $e) {
                system_log(sprintf('出错：<red>%s</red>', $e->getMessage()));
                $this->sendExceptionReport($e);
            } catch (\Exception $e) {
                system_log(sprintf('出错：<red>%s</red>', $e->getMessage()), $e->getTrace());
                $this->sendExceptionReport($e);
            }
        }
    }
}