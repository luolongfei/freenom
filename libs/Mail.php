<?php
/**
 * 邮件
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2019/5/12
 * @time 16:38
 */

namespace Luolongfei\Lib;

use Luolongfei\App\Exceptions\LlfException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

class Mail
{
    /**
     * @var PHPMailer
     */
    protected static $mail;

    /**
     * @return PHPMailer
     * @throws MailException
     * @throws \Exception
     */
    public static function mail()
    {
        if (!self::$mail instanceof PHPMailer) {
            self::$mail = new PHPMailer(true);

            // 邮件服务配置
            $username = config('mail.username');
            $password = config('mail.password');
            if (stripos($username, '@gmail.com') !== false) {
                $host = 'smtp.gmail.com';
                $secure = 'tls';
                $port = 587;
            } else if (stripos($username, '@qq.com') !== false) {
                $host = 'smtp.qq.com';
                $secure = 'tls';
                $port = 587;
            } else if (stripos($username, '@163.com') !== false) {
                $host = 'smtp.163.com';
                $secure = 'ssl';
                $port = 465;
            } else if (stripos($username, '@vip.163.com') !== false) {
                $host = 'smtp.vip.163.com';
                $secure = 'ssl';
                $port = 465;
            } else if (stripos($username, '@outlook.com') !== false) {
                $host = 'smtp.office365.com';
                $secure = 'starttls';
                $port = 587;
            } else {
                throw new \Exception('不受支持的邮箱。目前仅支持谷歌邮箱、QQ邮箱以及163邮箱，推荐使用谷歌邮箱。');
            }

            self::$mail->SMTPDebug = config('debug') ? 2 : 0; // Debug 0：关闭 1：客户端信息 2：客户端和服务端信息
            self::$mail->isSMTP(); // 告诉PHPMailer使用SMTP
            self::$mail->Host = $host; // SMTP服务器
            self::$mail->SMTPAuth = true; // 启用SMTP身份验证
            self::$mail->Username = $username; // 账号
            self::$mail->Password = $password; // 密码或授权码
            self::$mail->SMTPSecure = $secure; // 将加密系统设置为使用 - ssl（不建议使用）或tls
            self::$mail->Port = $port; // 设置SMTP端口号 - tsl使用587端口，ssl使用465端口
            self::$mail->CharSet = 'UTF-8'; // 防止中文邮件乱码
            self::$mail->setLanguage('zh_cn', VENDOR_PATH . '/phpmailer/phpmailer/language/'); // 设置语言
            self::$mail->setFrom($username, 'im robot'); // 发件人
        }

        return self::$mail;
    }

    /**
     * 发送邮件
     *
     * @param string $subject 标题
     * @param string | array $content 正文
     * @param string $to 收件人，选传
     * @param string $template 模板，选传
     *
     * @return bool
     * @throws \Exception
     */
    public static function send($subject, $content, $to = '', $template = '')
    {
        if (config('mail.enable') === false) {
            system_log('由于没有启用邮件功能，故本次不通过邮件送信。');

            return false;
        }

        $to = $to ?: config('mail.to');
        if (!$to) {
            throw new LlfException(env('ON_GITHUB_ACTIONS') ? 34520011 : 34520012);
        }

        self::mail()->addAddress($to, config('mail.toName', '主人')); // 添加收件人，参数2选填
        self::mail()->addReplyTo(config('mail.replyTo', 'mybsdc@qq.com'), config('mail.replyToName', '作者')); // 备用回复地址，收到的回复的邮件将被发到此地址

        /**
         * 抄送和密送都是添加收件人，抄送方式下，被抄送者知道除被密送者外的所有的收件人，密送方式下，
         * 被密送者知道所有的被抄送者，但不知道其它的被密送者。
         * 抄送好比@，密送好比私信。
         */
//        self::mail()->addCC('cc@example.com'); // 抄送
//        self::mail()->addBCC('bcc@example.com'); // 密送

        // 添加附件，参数2选填
//        self::mail()->addAttachment('README.md', '说明.txt');

        // 内容
        self::mail()->Subject = $subject; // 标题

        /**
         * 正文
         * 使用html文件内容作为正文，其中的图片将被base64编码，另确保html样式为内联形式，且某些样式可能需要!important方能正常显示，
         * msgHTML方法的第二个参数指定html内容中图片的路径，在转换时会拼接html中图片的相对路径得到完整的路径，最右侧无需“/”，PHPMailer
         * 源码里有加。css中的背景图片不会被转换，这是PHPMailer已知问题，建议外链。
         * 此处也可替换为：
         * self::mail()->isHTML(true); // 设为html格式
         * self::mail()->Body = '正文'; // 支持html
         * self::mail()->AltBody = 'This is an HTML-only message. To view it, activate HTML in your email application.'; // 纯文本消息正文。不支持html预览的邮件客户端将显示此预览消息，其它情况将显示正常的body
         */
        $template = file_get_contents(RESOURCES_PATH . '/mail/' . ($template ?: 'default') . '.html');
        if (is_array($content)) {
            array_unshift($content, $template);
            $message = call_user_func_array('sprintf', $content);
        } else if (is_string($content)) {
            $message = $content;
        } else {
            throw new MailException('邮件内容格式错误，仅支持传入数组或字符串。');
        }

        self::mail()->msgHTML($message, APP_PATH . '/mail');

        if (!self::mail()->send()) throw new MailException(self::mail()->ErrorInfo);

        return true;
    }
}
