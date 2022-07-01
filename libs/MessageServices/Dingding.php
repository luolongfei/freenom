<?php
/**
 * 钉钉
 *
 */

namespace Luolongfei\Libs\MessageServices;

use Luolongfei\App\Exceptions\LlfException;
use Luolongfei\Libs\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;
use Luolongfei\Libs\Connector\MessageGateway;

class Dingding extends MessageGateway
{
    /**
     * 送信
     *
     * @param string $content
     * @param string $subject
     * @param integer $type
     * @param array $data
     * @param string|null $recipient
     * @param mixed ...$params
     *
     * @return bool
     * @throws LlfException
     * @throws MailException
     */
    public function send(string $content, string $subject = '', int $type = 1, array $data = [], ?string $recipient = null, ...$params)
    {
        $this->check($content, $data);
        $content = $content ?: $subject;
        $webhook = config('message.dingding.webhook');
        try {
            $send_data = [
                'msgtype' => 'text',
                'text' => ['content' => $content]];
            $data_string = json_encode($send_data);
            $result = $this->request_by_curl($webhook, $data_string);
//            echo $result;
            system_log(lang('100135') . $result);
            return true;
        } catch (\Exception $e) {
            system_log(lang('100135') . $e->getMessage());
            return false;
        }
    }

    function request_by_curl($remote_server, $post_string, $header = ['Content-Type' => 'application/json;charset=utf-8'])
    {
        $headers = [];
        foreach ($header as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_server);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}
