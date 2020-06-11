<?php
/**
 * Telegram Bot
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2020/2/3
 * @time 15:23
 */

namespace Luolongfei\Lib;

use GuzzleHttp\Client;

class TelegramBot
{
    const TIMEOUT = 34.52;

    /**
     * @var TelegramBot
     */
    protected static $instance;

    /**
     * @var string chat_id
     */
    protected $chatID;

    /**
     * @var string TelegramBot token
     */
    protected $token;

    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $this->chatID = config('telegram.chatID');
        $this->token = config('telegram.token');

        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'cookies' => false,
            'timeout' => self::TIMEOUT,
            'verify' => config('verifySSL'),
//            'http_errors' => false,
            'debug' => config('debug')
        ]);
    }

    protected static function instance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 发送消息
     *
     * @param string $content 支持markdown语法，但记得对非标记部分进行转义
     * @param string $chatID 可单独指定chat_id参数
     * @param bool $isMarkdown 默认内容为Markdown格式，传否则为Html格式
     * @desc 注意对markdown标记占用的字符进行转义，否则无法正确发送，根据官方说明，以下字符如果不想被 Telegram Bot 识别为markdown标记，
     * 应转义后传入，官方说明如下：
     * In all other places characters '_‘, ’*‘, ’[‘, ’]‘, ’(‘, ’)‘, ’~‘, ’`‘, ’>‘, ’#‘, ’+‘, ’-‘, ’=‘, ’|‘,
     * ’{‘, ’}‘, ’.‘, ’!‘ must be escaped with the preceding character ’\'.
     * 如果你不转义，且恰好又不是正确的markdown语法，那 Telegram Bot 就只有报错了您勒
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
     * 需要注意的是，普通markdown语法中加粗字体使用的是“**正文**”的形式，但是 Telegram Bot 中是“*加粗我呀*”的形式，更多相关信息请
     * 参考官网：https://core.telegram.org/bots/api#sendmessage
     * 另外我干掉了“_”、“~”、“-”、“.”和“>”关键字，分别对应斜体、删除线、无序列表、有序列表和引用符号，这几个我可能用不上:)
     *
     * @return bool
     */
    public static function send(string $content, $chatID = '', $isMarkdown = true)
    {
        if (config('telegram.enable') === false) {
            system_log('由于没有启用 Telegram Bot 功能，故本次不通过 Telegram Bot 送信。');

            return false;
        }

        if ($isMarkdown) {
            // 这几个我可能用不上的markdown关键字我就直接干掉了
            $content = preg_replace('/([.>~_-])/i', '\\\\$1', $content);
        }

        $telegramBot = self::instance();

        $response = $telegramBot->client->post(
            sprintf('https://api.telegram.org/bot%s/sendMessage', $telegramBot->token),
            [
                'form_params' => [
                    'chat_id' => $chatID ? $chatID : $telegramBot->chatID,
                    'text' => $content,
                    'parse_mode' => $isMarkdown ? 'MarkdownV2' : 'HTML',
                    'disable_web_page_preview' => true,
                    'disable_notification' => false
                ],
            ]
        );
        $rp = json_decode((string)$response->getBody(), true);

        return $rp['ok'] ?? false;
    }
}