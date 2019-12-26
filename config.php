<?php
/**
 * 配置文件
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2018/7/28
 * @time 17:40
 */

return [
    'userInfo' => [
        'name' => '罗叔叔',
        'username' => '593198779@qq.com', // freenom账号
        'password' => 'xxxxxx', // freenom密码
        'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.84 Safari/537.36',
    ],
    'mail' => [
        'from' => 'llf.push@gmail.com', // 发件人
        'to' => 'mybsdc@qq.com', // 收件人
        'replyTo' => 'mybsdc@gmail.com', // 接收回复的邮箱
        'username' => 'llf.push@gmail.com', // 邮箱账户
        'password' => 'xxxxxx', // 邮箱密码
        'debug' => 0, // debug，当邮件无法发送的情况下开启此项观察命令行界面提示信息，正式环境应关闭 0：关闭 1：客户端信息 2：客户端和服务端信息
    ],
    'telegram' => [
        'enable' => 'true',
        'chatid' => '',
        'token' => '',
    ],
];
