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
use Luolongfei\Libs\Log;
use Luolongfei\Libs\Message;

class FreeNom extends Base
{
    const VERSION = 'v0.4.4';

    const TIMEOUT = 33;

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
     * @var Client
     */
    protected $client;

    /**
     * @var CookieJar | bool
     */
    protected $jar = true;

    /**
     * @var string FreeNom 账户
     */
    protected $username;

    /**
     * @var string FreeNom 密码
     */
    protected $password;

    /**
     * @var FreeNom
     */
    private static $instance;

    /**
     * @return FreeNom
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
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
            'verify' => config('verify_ssl'),
            'debug' => config('debug'),
            'proxy' => config('freenom_proxy'),
        ]);

        system_log(sprintf('当前程序版本 %s', self::VERSION));
    }

    private function __clone()
    {
    }

    /**
     * 登录
     *
     * @param string $username
     * @param string $password
     *
     * @return bool
     * @throws LlfException
     */
    protected function login(string $username, string $password)
    {
        try {
            $this->client->post(self::LOGIN_URL, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Referer' => 'https://my.freenom.com/clientarea.php'
                ],
                'form_params' => [
                    'username' => $username,
                    'password' => $password
                ],
                'cookies' => $this->jar
            ]);
        } catch (\Exception $e) {
            throw new LlfException(34520002, $e->getMessage());
        }

        if (empty($this->jar->getCookieByName('WHMCSZH5eHTGhfvzP')->getValue())) {
            throw new LlfException(34520002, lang('error_msg.100001'));
        }

        return true;
    }

    /**
     * 匹配获取所有域名
     *
     * @param string $domainStatusPage
     *
     * @return array
     * @throws LlfException
     */
    protected function getAllDomains(string $domainStatusPage)
    {
        if (!preg_match_all(self::DOMAIN_INFO_REGEX, $domainStatusPage, $allDomains, PREG_SET_ORDER)) {
            throw new LlfException(34520003);
        }

        return $allDomains;
    }

    /**
     * 获取匹配 token
     *
     * 据观察，每次登录后此 token 不会改变，故可以只获取一次，多次使用
     *
     * @param string $domainStatusPage
     *
     * @return string
     * @throws LlfException
     */
    protected function getToken(string $domainStatusPage)
    {
        if (!preg_match(self::TOKEN_REGEX, $domainStatusPage, $matches)) {
            throw new LlfException(34520004);
        }

        return $matches['token'];
    }

    /**
     * 获取域名状态页面
     *
     * @return string
     * @throws LlfException
     */
    protected function getDomainStatusPage()
    {
        try {
            $resp = $this->client->get(self::DOMAIN_STATUS_URL, [
                'headers' => [
                    'Referer' => 'https://my.freenom.com/clientarea.php'
                ],
                'cookies' => $this->jar
            ]);

            $page = (string)$resp->getBody();
        } catch (\Exception $e) {
            throw new LlfException(34520013, $e->getMessage());
        }

        if (!preg_match(self::LOGIN_STATUS_REGEX, $page)) {
            throw new LlfException(34520009);
        }

        return $page;
    }

    /**
     * 续期所有域名
     *
     * @param array $allDomains
     * @param string $token
     *
     * @return bool
     */
    public function renewAllDomains(array $allDomains, string $token)
    {
        $renewalSuccessArr = [];
        $renewalFailuresArr = [];
        $domainStatusArr = [];

        foreach ($allDomains as $d) {
            $domain = $d['domain'];
            $days = (int)$d['days'];
            $id = $d['id'];

            // 免费域名只允许在到期前 14 天内续期
            if ($days <= 14) {
                $renewalResult = $this->renew($id, $token);

                sleep(1);

                if ($renewalResult) {
                    $renewalSuccessArr[] = $domain;

                    continue; // 续期成功的域名无需记录过期天数
                } else {
                    $renewalFailuresArr[] = $domain;
                }
            }

            // 记录域名过期天数
            $domainStatusArr[$domain] = $days;
        }

        // 存在续期操作
        if ($renewalSuccessArr || $renewalFailuresArr) {
            $data = [
                'username' => $this->username,
                'renewalSuccessArr' => $renewalSuccessArr,
                'renewalFailuresArr' => $renewalFailuresArr,
                'domainStatusArr' => $domainStatusArr,
            ];
            $result = Message::send('', '主人，我刚刚帮你续期域名啦~', 2, $data);

            system_log(sprintf(
                '恭喜，成功续期 <green>%d</green> 个域名，失败 <green>%d</green> 个域名。%s',
                count($renewalSuccessArr),
                count($renewalFailuresArr),
                $result ? '详细的续期结果已送信成功，请注意查收。' : ''
            ));

            Log::info(sprintf("账户：%s\n续期结果如下：\n", $this->username), $data);

            return true;
        }

        // 不存在续期操作
        if (config('notice_freq') === 1) {
            $data = [
                'username' => $this->username,
                'domainStatusArr' => $domainStatusArr,
            ];
            Message::send('', '报告，今天没有域名需要续期', 3, $data);
        } else {
            system_log('当前通知频率为「仅当有续期操作时」，故本次不会推送通知');
        }

        system_log(sprintf('%s：<green>执行成功，今次没有需要续期的域名。</green>', $this->username));

        return true;
    }

    /**
     * 续期单个域名
     *
     * @param int $id
     * @param string $token
     *
     * @return bool
     */
    protected function renew(int $id, string $token)
    {
        try {
            $resp = $this->client->post(self::RENEW_DOMAIN_URL, [
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

            $resp = (string)$resp->getBody();

            return stripos($resp, 'Order Confirmation') !== false;
        } catch (\Exception $e) {
            $errorMsg = sprintf('续期请求出错：%s，域名 ID：%s（账户：%s）', $e->getMessage(), $id, $this->username);
            system_log($errorMsg);
            Message::send($errorMsg);

            return false;
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
     * 获取 FreeNom 账户信息
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
     * @param $e \Exception|LlfException
     */
    private function sendExceptionReport($e)
    {
        Message::send(sprintf(
            '具体是在%s文件的第%d行，抛出了一个异常。异常的内容是%s，快去看看吧。（账户：%s）',
            $e->getFile(),
            $e->getLine(),
            $e->getMessage(),
            $this->username
        ), '主人，出错了，' . $e->getMessage());
    }

    /**
     * @throws LlfException
     * @throws \Exception
     */
    public function handle()
    {
        $accounts = $this->getAccounts();

        system_log(sprintf('共发现 <green>%d</green> 个 freenom 账户，处理中', count($accounts)));

        foreach ($accounts as $account) {
            try {
                $this->username = $account['username'];
                $this->password = $account['password'];

                $this->jar = new CookieJar(); // 所有请求共用一个 CookieJar 实例
                $this->login($this->username, $this->password);

                $domainStatusPage = $this->getDomainStatusPage();
                $allDomains = $this->getAllDomains($domainStatusPage);
                $token = $this->getToken($domainStatusPage);

                $this->renewAllDomains($allDomains, $token);
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
