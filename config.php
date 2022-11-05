<?php
/**
 * 配置
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2019/3/2
 * @time 11:39
 */

return [
    'message' => [
        /**
         * 邮箱配置
         */
        'mail' => [
            /**
             * 目前机器人邮箱账户支持谷歌邮箱、QQ邮箱、163邮箱以及Outlook邮箱，程序会自动判断填入的邮箱类型并使用合适的配置。也可以自定义邮箱配置。
             * 注意，QQ邮箱与163邮箱均使用账户加授权码的方式登录，谷歌邮箱使用账户加密码的方式登录，请知悉。
             */
            'to' => env('TO'), // 用于接收通知的邮箱
            'recipient_name' => '主人', // 收件人名字
            'username' => env('MAIL_USERNAME'), // 机器人邮箱账户
            'password' => env('MAIL_PASSWORD'), // 机器人邮箱密码或授权码
            'enable' => (int)env('MAIL_ENABLE'), // 是否启用，默认启用
            'not_enabled_tips' => env('MAIL_USERNAME') && env('MAIL_PASSWORD'), // 提醒未启用
            // 'reply_to' => 'mybsdc@qq.com', // 接收回复的邮箱
            // 'reply_to_name' => '作者', // 接收回复的人名
            'host' => env('MAIL_HOST'), // 邮件 SMTP 服务器
            'port' => env('MAIL_PORT'), // 邮件 SMTP 端口
            'encryption' => env('MAIL_ENCRYPTION'), // 邮件加密方式
            'class' => \Luolongfei\Libs\MessageServices\Mail::class,
            'name' => lang('100064'),
        ],

        /**
         * Telegram Bot
         */
        'telegram' => [
            'chat_id' => env('TELEGRAM_CHAT_ID'), // 你的chat_id，通过发送“/start”给@userinfobot可以获取自己的id
            'token' => env('TELEGRAM_BOT_TOKEN'), // Telegram Bot 的 token
            'enable' => (int)env('TELEGRAM_BOT_ENABLE'), // 是否启用，默认不启用
            'not_enabled_tips' => env('TELEGRAM_CHAT_ID') && env('TELEGRAM_BOT_TOKEN'), // 提醒未启用
            'class' => \Luolongfei\Libs\MessageServices\TelegramBot::class,
            'name' => lang('100065'),
            'proxy' => env('TELEGRAM_PROXY') ?: null,
            'host' => env('CUSTOM_TELEGRAM_HOST') ?: 'api.telegram.org',
        ],

        /**
         * 企业微信
         */
        'wechat' => [
            'corp_id' => env('WECHAT_CORP_ID'), // 企业 ID
            'corp_secret' => env('WECHAT_CORP_SECRET'), // 企业微信应用的凭证密钥
            'agent_id' => (int)env('WECHAT_AGENT_ID'), // 企业微信应用 ID
            'enable' => (int)env('WECHAT_ENABLE'), // 是否启用，默认不启用
            'not_enabled_tips' => env('WECHAT_CORP_ID') && env('WECHAT_CORP_SECRET') && env('WECHAT_AGENT_ID'), // 提醒未启用
            'class' => \Luolongfei\Libs\MessageServices\WeChat::class,
            'name' => lang('100066'),
        ],

        /**
         * Server 酱
         */
        'sct' => [
            'sct_send_key' => env('SCT_SEND_KEY'), // SendKey
            'enable' => (int)env('SCT_ENABLE'), // 是否启用，默认不启用
            'not_enabled_tips' => (bool)env('SCT_SEND_KEY'), // 提醒未启用
            'class' => \Luolongfei\Libs\MessageServices\ServerChan::class,
            'name' => lang('100067'),
        ],

        /**
         * Bark 送信
         */
        'bark' => [
            'bark_key' => (string)env('BARK_KEY'), // 打开 Bark App，注册设备后看到的 Key
            'bark_url' => (string)env('BARK_URL'), // Bark 域名
            'bark_is_archive' => env('BARK_IS_ARCHIVE') === '' ? null : (int)env('BARK_IS_ARCHIVE'),
            'bark_group' => env('BARK_GROUP') === '' ? null : env('BARK_GROUP'),
            'bark_level' => env('BARK_LEVEL'),
            'bark_icon' => env('BARK_ICON') === '' ? null : env('BARK_ICON'),
            'bark_jump_url' => env('BARK_JUMP_URL') === '' ? null : env('BARK_JUMP_URL'),
            'bark_sound' => env('BARK_SOUND') === '' ? null : env('BARK_SOUND'),
            'enable' => (int)env('BARK_ENABLE'), // 是否启用，默认不启用
            'not_enabled_tips' => env('BARK_KEY') && env('BARK_URL'), // 提醒未启用
            'class' => \Luolongfei\Libs\MessageServices\Bark::class,
            'name' => lang('100068'),
        ],

        /**
         * Pushplus
         */
        'pushplus' => [
            'pushplus_key' => env('PUSHPLUS_KEY'), // SendKey
            'enable' => (int)env('PUSHPLUS_ENABLE'), // 是否启用，默认不启用
            'not_enabled_tips' => (bool)env('PUSHPLUS_KEY'), // 提醒未启用
            'class' => \Luolongfei\Libs\MessageServices\Pushplus::class,
            'name' => lang('100136'),
        ],
    ],
    'custom_language' => env('CUSTOM_LANGUAGE', 'zh'),
    'notice_freq' => (int)env('NOTICE_FREQ', 1), // 通知频率 0：仅当有续期操作的时候 1：每次执行
    'verify_ssl' => (bool)env('VERIFY_SSL', 0), // 请求时验证 SSL 证书行为，默认不验证，防止服务器证书过期或证书颁布者信息不全导致无法发出请求
    'debug' => (bool)env('DEBUG'),
    'freenom_proxy' => env('FREENOM_PROXY') ?: null, // FreeNom 代理，针对国内网络情况，可选择代理访问
    'new_version_detection' => (bool)env('NEW_VERSION_DETECTION', 1),
];