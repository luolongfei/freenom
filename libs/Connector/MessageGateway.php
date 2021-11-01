<?php
/**
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2021/10/20
 * @time 13:34
 */

namespace Luolongfei\Libs\Connector;

abstract class MessageGateway implements MessageServiceInterface
{
    /**
     * 根据模板生成送信内容
     *
     * @param array $data 数据
     * @param string $template 模板内容
     *
     * @return string
     */
    public function genMessageContent(array $data, string $template)
    {
        array_unshift($data, $template);

        return call_user_func_array('sprintf', $data);
    }
}
