<?php
/**
 * 配置
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2019/3/2
 * @time 11:39
 */

return [
    /**
     * 邮箱配置
     */
    'mail' => [
        /**
         * 目前机器人邮箱账户支持谷歌邮箱、QQ邮箱以及163邮箱，程序会自动判断填入的邮箱类型并使用合适的配置。注意，QQ邮箱与163邮箱均使用
         * 账户加授权码的方式登录，谷歌邮箱使用账户加密码的方式登录，请知悉。
         */
        'to' => env('TO'), // 用于接收通知的邮箱
        'toName' => '主人', // 收件人名字
        'username' => env('MAIL_USERNAME'), // 机器人邮箱账户
        'password' => env('MAIL_PASSWORD'), // 机器人邮箱密码或授权码
        'enable' => env('MAIL_ENABLE'), // 是否启用，默认启用

        // 'replyTo' => 'mybsdc@qq.com', // 接收回复的邮箱
        // 'replyToName' => '作者', // 接收回复的人名
    ],

    /**
     * Telegram Bot
     */
    'telegram' => [
        'chatID' => env('TELEGRAM_CHAT_ID'), // 你的chat_id，通过发送“/start”给@userinfobot可以获取自己的id
        'token' => env('TELEGRAM_BOT_TOKEN'), // Telegram Bot 的 token
        'enable' => env('TELEGRAM_BOT_ENABLE') // 是否启用，默认不启用
    ],

    'locale' => 'zh', // 指定语言包，位于resources/lang/目录下
    'noticeFreq' => env('NOTICE_FREQ'), // 通知频率 0：仅当有续期操作的时候 1：每次执行
    'verifySSL' => env('VERIFY_SSL'), // 请求时验证 SSL 证书行为，默认不验证，防止服务器证书过期或证书颁布者信息不全导致无法发出请求
    'debug' => env('DEBUG'),
];