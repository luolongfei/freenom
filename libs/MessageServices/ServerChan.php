<?php
/**
 * Server 酱
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2021/11/3
 * @time 9:59
 */

namespace Luolongfei\Libs\MessageServices;

use GuzzleHttp\Client;
use Luolongfei\Libs\Connector\MessageGateway;

class ServerChan extends MessageGateway
{
    const TIMEOUT = 33;

    /**
     * @var string SendKey
     */
    protected $sendKey;

    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $this->sendKey = config('message.sct.sct_send_key');

        $this->client = new Client([
            'cookies' => false,
            'timeout' => self::TIMEOUT,
            'verify' => config('verify_ssl'),
            'debug' => config('debug'),
        ]);
    }

    /**
     * 生成域名状态 MarkDown 完整文本
     *
     * @param string $username
     * @param array $domainStatus
     *
     * @return string
     */
    public function genDomainStatusFullMarkDownText(string $username, array $domainStatus)
    {
        $markDownText = sprintf("我刚刚帮小主看了一下，账户 [%s](#) 今天并没有需要续期的域名。所有域名情况如下：\n\n", $username);

        $markDownText .= $this->genDomainStatusMarkDownText($domainStatus);

        $markDownText .= $this->getMarkDownFooter();

        return $markDownText;
    }

    /**
     * 获取 MarkDown 页脚
     *
     * @return string
     */
    public function getMarkDownFooter()
    {
        $footer = '';

        $footer .= "\n更多信息可以参考 [Freenom官网](https://my.freenom.com/domains.php?a=renewals) 哦~";
        $footer .= "\n\n（如果你不想每次执行都收到推送，请将 .env 中 NOTICE_FREQ 的值设为 0，使程序只在有续期操作时才推送）";

        return $footer;
    }

    /**
     * 生成域名状态 MarkDown 文本
     *
     * @param array $domainStatus
     *
     * @return string
     */
    public function genDomainStatusMarkDownText(array $domainStatus)
    {
        if (empty($domainStatus)) {
            return "无数据。\n";
        }

        $domainStatusMarkDownText = '';

        foreach ($domainStatus as $domain => $daysLeft) {
            $domainStatusMarkDownText .= sprintf('[%s](http://%s) 还有 *%d* 天到期，', $domain, $domain, $daysLeft);
        }

        $domainStatusMarkDownText = rtrim($domainStatusMarkDownText, '，') . "。\n";

        return $domainStatusMarkDownText;
    }

    /**
     * 生成域名续期结果 MarkDown 文本
     *
     * @param string $username
     * @param array $renewalSuccessArr
     * @param array $renewalFailuresArr
     * @param array $domainStatus
     *
     * @return string
     */
    public function genDomainRenewalResultsMarkDownText(string $username, array $renewalSuccessArr, array $renewalFailuresArr, array $domainStatus)
    {
        $text = sprintf("账户 [%s](#) 这次续期的结果如下\n\n", $username);

        if ($renewalSuccessArr) {
            $text .= '续期成功：';
            $text .= $this->genDomainsMarkDownText($renewalSuccessArr);
        }

        if ($renewalFailuresArr) {
            $text .= '续期出错：';
            $text .= $this->genDomainsMarkDownText($renewalFailuresArr);
        }

        $text .= "\n今次无需续期的域名及其剩余天数如下所示：\n\n";
        $text .= $this->genDomainStatusMarkDownText($domainStatus);

        $text .= $this->getMarkDownFooter();

        return $text;
    }

    /**
     * 生成域名 MarkDown 文本
     *
     * @param array $domains
     *
     * @return string
     */
    public function genDomainsMarkDownText(array $domains)
    {
        $domainsMarkDownText = '';

        foreach ($domains as $domain) {
            $domainsMarkDownText .= sprintf("[%s](http://%s) ", $domain, $domain);
        }

        $domainsMarkDownText = trim($domainsMarkDownText, ' ') . "\n";

        return $domainsMarkDownText;
    }

    /**
     * 送信
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
            $content = $this->genDomainRenewalResultsMarkDownText($data['username'], $data['renewalSuccessArr'], $data['renewalFailuresArr'], $data['domainStatusArr']);
        } else if ($type === 3) {
            $content = $this->genDomainStatusFullMarkDownText($data['username'], $data['domainStatusArr']);
        } else {
            throw new \Exception(lang('error_msg.100003'));
        }

        $subject = $subject === '' ? mb_substr($content, 0, 12) . '...' : $subject;

        try {
            $resp = $this->client->post(
                sprintf('https://sctapi.ftqq.com/%s.send', $this->sendKey),
                [
                    'form_params' => [
                        'title' => $subject,
                        'desp' => str_replace("\n", "\n\n", $content), // Server酱 接口限定，两个 \n 等于一个换行
                    ],
                ]
            );

            $resp = json_decode((string)$resp->getBody(), true);

            if (isset($resp['code']) && $resp['code'] === 0) {
                return true;
            }

            throw new \Exception($resp['message'] ?? '未知原因');
        } catch (\Exception $e) {
            system_log('Server酱 消息发送失败：<red>' . $e->getMessage() . '</red>');

            return false;
        }
    }
}
