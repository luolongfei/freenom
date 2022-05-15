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

    /**
     * 参数数据检查
     *
     * @param string $content
     * @param array $data
     *
     * @throws \Exception
     */
    public function check(string $content, array $data)
    {
        if ($content === '' && empty($data)) {
            throw new \Exception(lang('100002'));
        }
    }

    /**
     * 换行转 <br>
     *
     * @param string $content
     *
     * @return string
     */
    public function newLine2Br(string $content)
    {
        return preg_replace("/\n/u", '<br>', $content);
    }

    /**
     * 设置公共页脚
     *
     * @param $footer
     * @param $newline
     * @param $enablePushFreqTips
     *
     * @return void
     */
    public function setCommonFooter(&$footer, $newline = "\n", $enablePushFreqTips = true)
    {
        if ($enablePushFreqTips) {
            $footer .= $newline . $newline . lang('100133');
        }

        // 服务器信息相关文言
        if (env('SHOW_SERVER_INFO')) {
            $footer .= $newline . $newline . lang('100134');
            $footer .= $newline . get_ip_info();
        }
    }
}
