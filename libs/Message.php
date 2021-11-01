<?php
/**
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2021/10/20
 * @time 13:46
 */

namespace Luolongfei\Libs;

use Luolongfei\Libs\Connector\MessageServiceInterface;

/**
 * Class Message
 *
 * @method bool send(string $content, string $subject = '', int $type = 1, array $data = [], ?string $recipient = null, ...$params) 送信
 */
abstract class Message extends Base
{
    /**
     * @param $method
     * @param $params
     *
     * @throws \Exception
     */
    public static function __callStatic($method, $params)
    {
        foreach (config('message') as $conf) {
            if ($conf['enable'] !== 1) {
                system_log(sprintf('由于没有启用「%s」功能，故本次不通过「%s」送信。', $conf['name'], $conf['name']));

                continue;
            }

            $serviceInstance = self::getInstance($conf['class'], 'IS_MESSAGE_SERVICE');

            if (!$serviceInstance instanceof MessageServiceInterface) {
                throw new \Exception(sprintf('消息服务类 %s 必须继承并实现 MessageServiceInterface 接口', $conf['class']));
            }

            $serviceInstance->$method(...$params);
        }
    }
}