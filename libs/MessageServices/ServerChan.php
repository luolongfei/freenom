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
        $markDownText = sprintf(lang('100090'), $username);

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

        $footer .= lang('100091');

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
            return lang('100093');
        }

        $domainStatusMarkDownText = '';

        foreach ($domainStatus as $domain => $daysLeft) {
            $domainStatusMarkDownText .= sprintf(lang('100094'), $domain, $domain, $daysLeft);
        }

        $domainStatusMarkDownText = rtrim(rtrim($domainStatusMarkDownText, ' '), '，,') . lang('100095');

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
        $text = sprintf(lang('100096'), $username);

        if ($renewalSuccessArr) {
            $text .= lang('100097');
            $text .= $this->genDomainsMarkDownText($renewalSuccessArr);
        }

        if ($renewalFailuresArr) {
            $text .= lang('100098');
            $text .= $this->genDomainsMarkDownText($renewalFailuresArr);
        }

        $text .= lang('100099');
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

        $commonFooter = '';

        if ($type === 1 || $type === 4) {
            $this->setCommonFooter($commonFooter, "\n", false);
        } else if ($type === 2) {
            $this->setCommonFooter($commonFooter, "\n", false);
            $content = $this->genDomainRenewalResultsMarkDownText($data['username'], $data['renewalSuccessArr'], $data['renewalFailuresArr'], $data['domainStatusArr']);
        } else if ($type === 3) {
            $this->setCommonFooter($commonFooter);
            $content = $this->genDomainStatusFullMarkDownText($data['username'], $data['domainStatusArr']);
        } else {
            throw new \Exception(lang('100003'));
        }

        $content .= $commonFooter;

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

            throw new \Exception($resp['message'] ?? lang('100100'));
        } catch (\Exception $e) {
            system_log(sprintf(lang('100101'), $e->getMessage()));

            return false;
        }
    }
}
