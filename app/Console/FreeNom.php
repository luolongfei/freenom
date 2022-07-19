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
use Luolongfei\App\Exceptions\WarningException;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Luolongfei\Libs\Log;
use Luolongfei\Libs\Message;

class FreeNom extends Base
{
    const VERSION = 'v0.5';

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

    // 匹配无域名的正则
    const NO_DOMAIN_REGEX = '/<tr\sclass="carttablerow"><td\scolspan="5">(?P<msg>[^<]+)<\/td><\/tr>/i';

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

        system_log(sprintf(lang('100038'), self::VERSION));
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
            throw new LlfException(34520002, lang('100001'));
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
     * @throws WarningException
     */
    protected function getAllDomains(string $domainStatusPage)
    {
        if (preg_match(self::NO_DOMAIN_REGEX, $domainStatusPage, $m)) {
            throw new WarningException(34520014, [$this->username, $m['msg']]);
        }

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
            $result = Message::send('', lang('100039'), 2, $data);

            system_log(sprintf(
                lang('100040'),
                count($renewalSuccessArr),
                count($renewalFailuresArr),
                $result ? lang('100041') : ''
            ));

            Log::info(sprintf(lang('100042'), $this->username), $data);

            return true;
        }

        // 不存在续期操作
        if (config('notice_freq') === 1) {
            $data = [
                'username' => $this->username,
                'domainStatusArr' => $domainStatusArr,
            ];
            Message::send('', lang('100043'), 3, $data);
        } else {
            system_log(lang('100044'));
        }

        system_log(sprintf(lang('100045'), $this->username));

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
            $errorMsg = sprintf(lang('100046'), $e->getMessage(), $id, $this->username);
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
            lang('100047'),
            $e->getFile(),
            $e->getLine(),
            $e->getMessage(),
            $this->username
        ), lang('100048') . $e->getMessage());
    }

    /**
     * @throws LlfException
     * @throws \Exception
     */
    public function handle()
    {
        $accounts = $this->getAccounts();
        $totalAccounts = count($accounts);

        system_log(sprintf(lang('100049'), $totalAccounts));

        foreach ($accounts as $index => $account) {
            try {
                $this->username = $account['username'];
                $this->password = $account['password'];

                $num = $index + 1;
                system_log(sprintf(lang('100050'), get_local_num($num), $this->username, $num, $totalAccounts));

                $this->jar = new CookieJar(); // 所有请求共用一个 CookieJar 实例
                $this->login($this->username, $this->password);

                $domainStatusPage = $this->getDomainStatusPage();
                $allDomains = $this->getAllDomains($domainStatusPage);
                $token = $this->getToken($domainStatusPage);

                $this->renewAllDomains($allDomains, $token);
            } catch (WarningException $e) {
                system_log(sprintf(lang('100129'), $e->getMessage()));
            } catch (LlfException $e) {
                system_log(sprintf(lang('100051'), $e->getMessage()));
                $this->sendExceptionReport($e);
            } catch (\Exception $e) {
                system_log(sprintf(lang('100052'), $e->getMessage()), $e->getTrace());
                $this->sendExceptionReport($e);
            }
        }
    }
}
