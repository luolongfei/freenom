<?php
/**
 * 业务逻辑异常时抛出
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2018/8/10
 * @time 14:48
 */

namespace Luolongfei\App\Exceptions;

class LlfException extends \Exception
{
    public function __construct($code, $additional = null, \Exception $previous = null)
    {
        $message = lang('exception_msg.' . $code) ?: '';

        if ($additional !== null) {
            if (is_array($additional)) {
                array_unshift($additional, $message);
                $message = call_user_func_array('sprintf', $additional);
            } else if (is_string($additional) || is_numeric($additional)) {
                $message = sprintf($message, $additional);
            }
        }

        parent::__construct($message . "(Error code: {$code})", $code, $previous);
    }
}