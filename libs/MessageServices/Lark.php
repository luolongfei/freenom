<?php

/**
 * 飞书
 * 
 * @author suntory <suntory0902@gmail.com>
 * @link [https://open.feishu.cn/document/ukTMukTMukTM/ucTM5YjL3ETO24yNxkjN#383d6e48][飞书自定义机器人指南]
 */

namespace Luolongfei\Libs\MessageServices;

use GuzzleHttp\Client;
use Luolongfei\Libs\Connector\MessageGateway;
use Luolongfei\Libs\Log;

class Lark extends MessageGateway
{
    const TIMEOUT = 33;

    /**
     * @var string 飞书机器人 token
     */
    protected $token;

    /**
     * @var string 飞书机器人签名校验
     */
    protected $secret;

    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $this->token = config('message.lark.lark_token');
        $this->secret = config('message.lark.lark_secret');

        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'cookies' => false,
            'timeout' => self::TIMEOUT,
            'verify' => config('verify_ssl'),
            'debug' => config('debug')
        ]);
    }

    /**
     * 生成签名校验
     */
    protected function getSign(string $secret, int $timestamp)
    {
        return base64_encode(hash_hmac('sha256', '', ($timestamp . "\n" . $secret), true));
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
        $markDownText = sprintf(lang('100102'), $username);

        $markDownText .= $this->genDomainStatusMarkDownText($domainStatus);

        $markDownText .= $this->getMarkDownFooter();

        return $markDownText;
    }

    /**
     * 获取 MarkDown 页脚
     *
     * @param bool $isRenewalResult 是否续期结果，续期结果不用提醒调整推送频率
     *
     * @return string
     */
    public function getMarkDownFooter(bool $isRenewalResult = false)
    {
        $footer = '';

        $footer .= lang('100103');

        if (!$isRenewalResult) {
            $footer .= lang('100104');
        }

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
            return lang('100105');
        }

        $domainStatusMarkDownText = '';

        foreach ($domainStatus as $domain => $daysLeft) {
            $domainStatusMarkDownText .= sprintf(lang('100106'), $domain, $domain, $daysLeft);
        }

        $domainStatusMarkDownText = rtrim(rtrim($domainStatusMarkDownText, ' '), '，,') . lang('100107');

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
        $text = sprintf(lang('100108'), $username);

        if ($renewalSuccessArr) {
            $text .= lang('100109');
            $text .= $this->genDomainsMarkDownText($renewalSuccessArr);
        }

        if ($renewalFailuresArr) {
            $text .= lang('100110');
            $text .= $this->genDomainsMarkDownText($renewalFailuresArr);
        }

        $text .= lang('100111');
        $text .= $this->genDomainStatusMarkDownText($domainStatus);

        $text .= $this->getMarkDownFooter(true);

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

    public function send(string $content, string $subject = '', int $type = 1, array $data = [], ?string $recipient = null, ...$params)
    {
        Log::error("Start to send");
        $this->check($content, $data);

        if ($type === 1 || $type === 4) {
            // Do nothing
        } else if ($type === 2) {
            $content = $this->genDomainRenewalResultsMarkDownText($data['username'], $data['renewalSuccessArr'], $data['renewalFailuresArr'], $data['domainStatusArr']);
        } else if ($type === 3) {
            $content = $this->genDomainStatusFullMarkDownText($data['username'], $data['domainStatusArr']);
        } else {
            throw new \Exception(lang('100003'));
        }

        $isMarkdown = true;

        if ($subject !== '') {
            $content = $subject . "\n\n" . $content;
        }

        if ($isMarkdown) {
            // 这几个比较容易在正常文本里出现，而我不想每次都手动转义传入，所以直接干掉了
            $content = preg_replace('/([.>~_{}|`!+=#-])/u', '\\\\$1', $content);

            // 转义非链接格式的 [] 以及 ()
            $content = preg_replace_callback_array(
                [
                    '/(?<!\\\\)\[(?P<brackets>.*?)(?!\]\()(?<!\\\\)\]/' => function ($match) {
                        return '\\[' . $match['brackets'] . '\\]';
                    },
                    '/(?<!\\\\)(?<!\])\((?P<parentheses>.*?)(?<!\\\\)\)/' => function ($match) {
                        return '\\(' . $match['parentheses'] . '\\)';
                    }
                ],
                $content
            );
        }

        try {
            $card = [
                'config' => [
                    'wide_screen_mode' => true,
                ],
                'header' => [
                    "title" => [
                        "tag" => "plain_text",
                        "content" => "Freenom 续期通知"
                    ],
                    "template" => "blue"
                ],
                'elements' => [
                    [
                        'tag'  => 'div',
                        'text' => [
                            'content' => $content,
                            'tag'     => 'lark_md',
                        ],
                    ],
                ],
            ];

            $requestParams = array();
            if (!empty($this->secret)) {
                $timestamp = time();
                $sign = $this->getSign($this->secret, $timestamp);

                $requestParams['timestamp'] = $timestamp;
                $requestParams['sign'] = $sign;
            }
            $requestParams['msg_type'] = 'interactive';
            $requestParams['card'] = $card;

            $resp = $this->client->post(
                sprintf('https://open.feishu.cn/open-apis/bot/v2/hook/%s', $this->token),
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode($requestParams),
                ]
            );

            $resp = json_decode((string)$resp->getBody(), true);
            Log::error("Result", $resp);
            $ret = $resp['StatusCode'] == 0 ?? false;
            Log::error($ret);
            return true;
        } catch (\Exception $e) {
            Log::error("Error result", $e->getTrace());
            system_log(sprintf(lang('100112'), $e->getMessage()));

            return false;
        }
    }
}
