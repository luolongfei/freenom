<?php
/**
 * Telegram Bot
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2020/2/3
 * @time 15:23
 */

namespace Luolongfei\Libs\MessageServices;

use GuzzleHttp\Client;
use Luolongfei\Libs\Log;
use Luolongfei\Libs\Connector\MessageGateway;

class TelegramBot extends MessageGateway
{
    const TIMEOUT = 33;

    /**
     * @var string chat_id
     */
    protected $chatID;

    /**
     * @var string 机器人令牌
     */
    protected $token;

    /**
     * @var string Telegram 主机地址
     */
    protected $host;

    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $this->chatID = config('message.telegram.chat_id');
        $this->token = config('message.telegram.token');
        $this->host = $this->getTelegramHost();

        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'cookies' => false,
            'timeout' => self::TIMEOUT,
            'verify' => config('verify_ssl'),
            'debug' => config('debug'),
            'proxy' => config('message.telegram.proxy'),
        ]);
    }

    /**
     * 获取 Telegram 主机地址
     *
     * @return string
     */
    private function getTelegramHost()
    {
        $host = (string)config('message.telegram.host');

        if (preg_match('/^(?:https?:\/\/)?(?P<host>[^\/?\n]+)/iu', $host, $m)) {
            return $m['host'];
        }

        return 'api.telegram.org';
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
     * @return string
     */
    public function getMarkDownFooter()
    {
        $footer = '';

        $footer .= lang('100103');

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

        $domainStatusMarkDownText = rtrim(rtrim($domainStatusMarkDownText, ' '), "，,\n") . lang('100107');

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
     * 获取 MarkDown 表格映射的原始数组
     *
     * @param string $markDownTable
     *
     * @return array
     */
    public function getMarkDownRawArr(string $markDownTable)
    {
        $rawArr = [];
        $markDownTableArr = preg_split("/(?:\n|\r\n)+/", $markDownTable);

        foreach ($markDownTableArr as $row) {
            $row = (string)preg_replace('/^\s+|\s+$|\s+|(?<=\|)\s+|\s+(?=\|)/', '', $row);

            if ($row === '') {
                continue;
            }

            $rowArr = explode('|', trim($row, '|'));
            $rawArr[] = $rowArr;
        }

        return $rawArr;
    }

    /**
     * 送信
     *
     * @param string $content 支持 markdown 语法，但记得对非标记部分进行转义
     * @param string $subject
     * @param integer $type
     * @param array $data
     * @param string|null $recipient 可单独指定 chat_id 参数
     * @param mixed ...$params
     *
     * @desc
     * 注意对 markdown 标记占用的字符进行转义，否则无法正确发送，根据官方说明，以下字符如果不想被 Telegram Bot 识别为 markdown 标记，
     * 应转义后传入，官方说明如下：
     * In all other places characters '_‘, ’*‘, ’[‘, ’]‘, ’(‘, ’)‘, ’~‘, ’`‘, ’>‘, ’#‘, ’+‘, ’-‘, ’=‘, ’|‘,
     * ’{‘, ’}‘, ’.‘, ’!‘ must be escaped with the preceding character ’\'.
     * 如果不转义则电报返回 400 错误
     *
     * 官方markdown语法示例：
     * *bold \*text*
     * _italic \*text_
     * __underline__
     * ~strikethrough~
     * *bold _italic bold ~italic bold strikethrough~ __underline italic bold___ bold*
     * [inline URL](http://www.example.com/)
     * [inline mention of a user](tg://user?id=123456789)
     * `inline fixed-width code`
     * ```
     * pre-formatted fixed-width code block
     * ```
     * ```python
     * pre-formatted fixed-width code block written in the Python programming language
     * ```
     * 需要注意的是，普通 markdown 语法中加粗字体使用的是“**正文**”的形式，但是 Telegram Bot 中是“*加粗我*”的形式
     * 更多相关信息请参考官网：https://core.telegram.org/bots/api#sendmessage
     * 另外我干掉了“_”、“~”、“-”、“.”和“>”关键字，分别对应斜体、删除线、无序列表、有序列表和引用符号，因为这几个比较容易在正常文本里出现，而
     * 我又不想每次都手动转义传入，故做了自动转义处理，况且 telegram 大多不支持
     *
     * 由于 telegram bot 的 markdown 语法不支持表格（https://core.telegram.org/bots/api#markdownv2-style），故表格部分由我自行解析
     * 为字符形式的表格，坐等 telegram bot 支持表格
     *
     * @return bool
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

        $isMarkdown = true;

        // 使用可变参数控制 telegram 送信类型，一般不会用到
        if ($params && isset($params[1]) && $params[0] === 'TG') {
            $isMarkdown = $params[1];
        }

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
            $resp = $this->client->post(
                sprintf('https://%s/bot%s/sendMessage', $this->host, $this->token),
                [
                    'form_params' => [
                        'chat_id' => $recipient ?: $this->chatID,
                        'text' => $content, // Text of the message to be sent, 1-4096 characters after entities parsing
                        'parse_mode' => $isMarkdown ? 'MarkdownV2' : 'HTML',
                        'disable_web_page_preview' => true,
                        'disable_notification' => false
                    ],
                ]
            );

            $resp = json_decode((string)$resp->getBody(), true);

            return $resp['ok'] ?? false;
        } catch (\Exception $e) {
            system_log(sprintf(lang('100112'), $e->getMessage()));

            return false;
        }
    }
}
