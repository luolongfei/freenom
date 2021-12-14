<?php
/**
 * 企业微信
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2021/11/1
 * @time 17:38
 */

namespace Luolongfei\Libs\MessageServices;

use GuzzleHttp\Client;
use Luolongfei\Libs\Connector\MessageGateway;

class WeChat extends MessageGateway
{
    const TIMEOUT = 33;

    /**
     * @var string 企业 ID
     */
    protected $corpId;

    /**
     * @var string 企业微信应用的凭证密钥
     */
    protected $corpSecret;

    /**
     * @var integer 企业微信应用 ID
     */
    protected $agentId;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string 缓存 access_token 的文件
     */
    protected $accessTokenFile;

    public function __construct()
    {
        $this->corpId = config('message.wechat.corp_id');
        $this->corpSecret = config('message.wechat.corp_secret');
        $this->agentId = config('message.wechat.agent_id');

        $this->accessTokenFile = DATA_PATH . DS . 'wechat_access_token.txt';

        $this->client = new Client([
            'cookies' => false,
            'timeout' => self::TIMEOUT,
            'verify' => config('verify_ssl'),
            'debug' => config('debug'),
        ]);
    }

    /**
     * 获取 access_token 缓存
     *
     * 由于云函数环境中只有 /tmp 目录的读写权限，且每次运行结束后写入的内容不会被保留，故云函数无法真正做到通过文件缓存 access_token
     * 参考：https://cloud.tencent.com/document/product/583/9180
     *
     * @return string|null
     */
    protected function getAccessTokenCache()
    {
        if (!file_exists($this->accessTokenFile)) {
            return null;
        }

        $accessTokenFile = file_get_contents($this->accessTokenFile);

        if (!preg_match('/^WECHAT_ACCESS_TOKEN_EXPIRES_AT=(?P<expires_at>.*?)$/im', $accessTokenFile, $m)) {
            return null;
        }
        $expiresAt = (int)$m['expires_at'];

        if (!preg_match('/^WECHAT_ACCESS_TOKEN=(?P<access_token>.*?)$/im', $accessTokenFile, $m)) {
            return null;
        }

        if (time() + 5 > $expiresAt) {
            return null;
        }

        return $m['access_token'];
    }

    /**
     * 获取 access_token
     *
     * @param bool $force
     *
     * @return mixed|string
     * @throws \Exception
     */
    protected function getAccessToken($force = false)
    {
        if (!$force) {
            $accessToken = $this->getAccessTokenCache();

            if (!is_null($accessToken)) {
                return $accessToken;
            }
        }

        $resp = $this->client->get('https://qyapi.weixin.qq.com/cgi-bin/gettoken', [
            'query' => [
                'corpid' => $this->corpId,
                'corpsecret' => $this->corpSecret
            ],
        ]);

        $resp = $resp->getBody()->getContents();
        $resp = (array)json_decode($resp, true);

        if (isset($resp['errcode']) && $resp['errcode'] === 0 && isset($resp['access_token']) && isset($resp['expires_in'])) {
            $accessTokenFileText = sprintf("WECHAT_ACCESS_TOKEN=%s\nWECHAT_ACCESS_TOKEN_EXPIRES_AT=%s\n", $resp['access_token'], time() + $resp['expires_in']);
            if (file_put_contents($this->accessTokenFile, $accessTokenFileText) === false) {
                throw new \Exception('企业微信 access_token 写入失败：' . $this->accessTokenFile);
            }

            return $resp['access_token'];
        }

        throw new \Exception('获取企业微信 access_token 失败：' . ($resp['errmsg'] ?? '未知原因'));
    }

    /**
     * 生成域名文本
     *
     * @param array $domains
     *
     * @return string
     */
    public function genDomainsText(array $domains)
    {
        $domainsText = '';

        foreach ($domains as $domain) {
            $domainsText .= sprintf('<a href="http://%s">%s</a> ', $domain, $domain);
        }

        $domainsText = trim($domainsText, ' ') . "\n";

        return $domainsText;
    }

    /**
     * 获取页脚
     *
     * @return string
     */
    public function getFooter()
    {
        $footer = '';

        $footer .= "\n更多信息可以参考 <a href=\"https://my.freenom.com/domains.php?a=renewals\">Freenom官网</a> 哦~";
        $footer .= "\n\n（如果你不想每次执行都收到推送，请将 .env 中 NOTICE_FREQ 的值设为 0，使程序只在有续期操作时才推送）";

        return $footer;
    }

    /**
     * 生成域名状态文本
     *
     * @param array $domainStatus
     *
     * @return string
     */
    public function genDomainStatusText(array $domainStatus)
    {
        if (empty($domainStatus)) {
            return "无数据。\n";
        }

        $domainStatusText = '';

        foreach ($domainStatus as $domain => $daysLeft) {
            $domainStatusText .= sprintf('<a href="http://%s">%s</a> 还有 <a href="http://%s">%d</a> 天到期，', $domain, $domain, $domain, $daysLeft);
        }

        $domainStatusText = rtrim($domainStatusText, '，') . "。\n";

        return $domainStatusText;
    }

    /**
     * 生成域名续期结果文本
     *
     * @param string $username
     * @param array $renewalSuccessArr
     * @param array $renewalFailuresArr
     * @param array $domainStatus
     *
     * @return string
     */
    public function genDomainRenewalResultsText(string $username, array $renewalSuccessArr, array $renewalFailuresArr, array $domainStatus)
    {
        $text = sprintf("账户 <a href=\"https://www.google.com\">%s</a> 这次续期的结果如下\n\n", $username);

        if ($renewalSuccessArr) {
            $text .= '续期成功：';
            $text .= $this->genDomainsText($renewalSuccessArr);
        }

        if ($renewalFailuresArr) {
            $text .= '续期出错：';
            $text .= $this->genDomainsText($renewalFailuresArr);
        }

        $text .= "\n今次无需续期的域名及其剩余天数如下所示：\n\n";
        $text .= $this->genDomainStatusText($domainStatus);

        $text .= $this->getFooter();

        return $text;
    }

    /**
     * 生成域名状态完整文本
     *
     * @param string $username
     * @param array $domainStatus
     *
     * @return string
     */
    public function genDomainStatusFullText(string $username, array $domainStatus)
    {
        $markDownText = sprintf("我刚刚帮小主看了一下，账户 <a href=\"https://www.google.com\">%s</a> 今天并没有需要续期的域名。所有域名情况如下：\n\n", $username);

        $markDownText .= $this->genDomainStatusText($domainStatus);

        $markDownText .= $this->getFooter();

        return $markDownText;
    }

    /**
     * 送信
     *
     * 由于腾讯要求 markdown 语法消息必须使用 企业微信 APP 才能查看，然而我并不想单独安装 企业微信 APP，故本方法不使用 markdown 语法，
     * 而是直接使用纯文本 text 类型，纯文本类型里腾讯额外支持 a 标签，所以基本满足需求
     *
     * 参考：
     * https://work.weixin.qq.com/api/doc/90000/90135/91039
     * https://work.weixin.qq.com/api/doc/90000/90135/90236#%E6%96%87%E6%9C%AC%E6%B6%88%E6%81%AF
     *
     * @param string $content
     * @param string $subject
     * @param int $type
     * @param array $data
     * @param string|null $recipient
     * @param mixed ...$params
     *
     * @return bool
     * @throws \Exception
     */
    public function send(string $content, string $subject = '', int $type = 1, array $data = [], ?string $recipient = null, ...$params)
    {
        $this->check($content, $data);

        if ($type === 1 || $type === 4) {
            // Do nothing
        } else if ($type === 2) {
            $content = $this->genDomainRenewalResultsText($data['username'], $data['renewalSuccessArr'], $data['renewalFailuresArr'], $data['domainStatusArr']);
        } else if ($type === 3) {
            $content = $this->genDomainStatusFullText($data['username'], $data['domainStatusArr']);
        } else {
            throw new \Exception(lang('error_msg.100003'));
        }

        if ($subject !== '') {
            $content = $subject . "\n\n" . $content;
        }

        try {
            $accessToken = $this->getAccessToken();

            $body = [
                'touser' => '@all', // 可直接通过此地址获取 userId，指定接收用户，多个用户用“|”分割 https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token=ACCESS_TOKEN&fetch_child=FETCH_CHILD&department_id=1
                'msgtype' => 'text', // 消息类型，text 类型支持 a 标签以及 \n 换行，基本满足需求。由于腾讯要求 markdown 语法必须使用 企业微信APP 才能查看，不想安装，故弃之
                'agentid' => $this->agentId, // 企业应用的 ID，整型，可在应用的设置页面查看
                'text' => [
                    'content' => $content, // 消息内容，最长不超过 2048 个字节，超过将截断
                ],
                'enable_duplicate_check' => 1,
                'duplicate_check_interval' => 60,
            ];

            return $this->doSend($accessToken, $body);
        } catch (\Exception $e) {
            system_log('企业微信送信失败：<red>' . $e->getMessage() . '</red>');

            return false;
        }
    }

    /**
     * 执行送信
     *
     * @param string $accessToken
     * @param array $body
     * @param int $numOfRetries
     *
     * @return bool
     * @throws \Exception
     */
    private function doSend(string $accessToken, array $body, int &$numOfRetries = 0)
    {
        $resp = $this->client->post('https://qyapi.weixin.qq.com/cgi-bin/message/send', [
            'query' => [
                'access_token' => $accessToken
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($body),
        ]);

        $resp = (string)$resp->getBody();
        $resp = (array)json_decode($resp, true);

        if (!isset($resp['errcode']) || !isset($resp['errmsg'])) {
            throw new \Exception('企业微信接口未返回预期的数据响应，本次响应数据为：' . json_encode($resp, JSON_UNESCAPED_UNICODE));
        }

        if ($resp['errcode'] === 0) {
            return true;
        } else if ($resp['errcode'] === 40014) { // invalid access_token
            $accessToken = $this->getAccessToken(true);

            if ($numOfRetries > 2) {
                throw new \Exception('检测到多次提示 access_token 失效，可能是未能正确获取 access_token，请介入调查：' . $resp['errmsg']);
            }

            $numOfRetries++;

            return $this->doSend($accessToken, $body, $numOfRetries);
        }

        throw new \Exception($resp['errmsg']);
    }
}
