<?php
/**
 * 警告
 *
 * @author luolongf <luolongf@gmail.com>
 * @date 2022-02-22
 * @time 14:06
 */

namespace Luolongfei\App\Exceptions;

class WarningException extends \Exception
{
    public function __construct($code, $additional = null, \Exception $previous = null)
    {
        $message = lang('exception_msg.' . $code) ?: '';

        if ($additional !== null) {
            if (is_array($additional)) {
                array_unshift($additional, $message);
                $message = call_user_func_array('sprintf', $additional);
            } else if (is_string($additional)) {
                $message = sprintf($message, $additional);
            }
        }

        parent::__construct($message . "(Warning code: {$code})", $code, $previous);
    }
}
