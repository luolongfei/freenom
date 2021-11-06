<?php

/**
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2021/10/20
 * @time 11:46
 */

namespace Luolongfei\Libs\Connector;

/**
 * 所有消息类需要实现的接口
 */
interface MessageServiceInterface
{
    /**
     * 送信
     *
     * @param string $content
     * @param string $subject
     * @param integer $type 消息类型 1：普通消息 2：域名续期结果 3：无需续期，域名状态信件 4：升级通知
     * @param array $data
     * @param string|null $recipient
     * @param ...$params
     *
     * @return bool
     */
    public function send(string $content, string $subject = '', int $type = 1, array $data = [], ?string $recipient = null, ...$params);
}