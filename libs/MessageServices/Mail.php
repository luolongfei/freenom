<?php
/**
 * 邮件
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2019/5/12
 * @time 16:38
 */

namespace Luolongfei\Libs\MessageServices;

use Luolongfei\App\Exceptions\LlfException;
use Luolongfei\Libs\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;
use Luolongfei\Libs\Connector\MessageGateway;

class Mail extends MessageGateway
{
    /**
     * @var PHPMailer
     */
    private $phpMailerInstance;

    /**
     * @throws MailException
     */
    public function __construct()
    {
        $this->language = config('custom_language', 'zh');

        $this->noticeTemplatePath = sprintf('%s/mail/%s/notice.html', RESOURCES_PATH, $this->language);
        $this->successfulRenewalTemplatePath = sprintf('%s/mail/%s/successful_renewal.html', RESOURCES_PATH, $this->language);
        $this->noRenewalRequiredTemplatePath = sprintf('%s/mail/%s/no_renewal_required.html', RESOURCES_PATH, $this->language);

        $this->phpMailerInstance = new PHPMailer(true);

        $this->init();
    }

    /**
     * 初始化邮箱配置
     *
     * @throws MailException
     */
    protected function init()
    {
        $username = config('message.mail.username');
        $password = config('message.mail.password');

        list($host, $secure, $port) = $this->getBasicMailConf($username);

        $this->phpMailerInstance->SMTPDebug = config('debug') ? 2 : 0; // Debug 0：关闭 1：客户端信息 2：客户端和服务端信息
        $this->phpMailerInstance->isSMTP(); // 告诉 PHPMailer 使用 SMTP
        $this->phpMailerInstance->Host = $host; // SMTP 服务器
        $this->phpMailerInstance->SMTPAuth = true; // 启用 SMTP 身份验证
        $this->phpMailerInstance->Username = $username; // 账号
        $this->phpMailerInstance->Password = $password; // 密码或授权码
        $this->phpMailerInstance->SMTPSecure = $secure; // 将加密系统设置为使用 - ssl（不建议使用）或 tls
        $this->phpMailerInstance->Port = $port; // 设置 SMTP 端口号 - tsl 使用 587 端口，ssl 使用 465 端口
        $this->phpMailerInstance->CharSet = 'UTF-8'; // 防止中文邮件乱码
        $this->phpMailerInstance->setLanguage('zh_cn', VENDOR_PATH . '/phpmailer/phpmailer/language/'); // 设置语言
        $this->phpMailerInstance->setFrom($username, 'Im robot'); // 发件人
    }

    /**
     * 获取邮箱基本配置
     *
     * @param string $username
     *
     * @return array
     * @throws MailException
     */
    public function getBasicMailConf(string $username)
    {
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
            $host = config('message.mail.host');
            $secure = config('message.mail.encryption');
            $port = (int)config('message.mail.port');
            if (!($host && $secure && $port)) {
                throw new MailException(lang('100069'));
            }
        }

        return [$host, $secure, $port];
    }

    /**
     * 生成域名 html
     *
     * @param array $domains
     *
     * @return string
     */
    public function genDomainsHtml(array $domains)
    {
        $domainsHtml = '';

        foreach ($domains as $domain) {
            $domainsHtml .= sprintf('<a href="http://%s" rel="noopener" target="_blank">%s</a>', $domain, $domain);
        }

        return $domainsHtml;
    }

    /**
     * 生成域名状态 html
     *
     * @param array $domainStatus
     *
     * @return string
     */
    public function genDomainStatusHtml(array $domainStatus)
    {
        if (empty($domainStatus)) {
            return lang('100070');
        }

        $domainStatusHtml = '';

        foreach ($domainStatus as $domain => $daysLeft) {
            $domainStatusHtml .= sprintf(lang('100071'), $domain, $domain, $daysLeft);
        }

        $domainStatusHtml = rtrim(rtrim($domainStatusHtml, ' '), '，,') . lang('100072');

        return $domainStatusHtml;
    }

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
        $recipient = $recipient ?: config('message.mail.to');

        if (!$recipient) {
            throw new LlfException(34520012);
        }

        $this->check($content, $data);

        $this->phpMailerInstance->addAddress($recipient, config('message.mail.recipient_name', lang('100073'))); // 添加收件人，参数2选填
        $this->phpMailerInstance->addReplyTo(config('message.mail.reply_to', 'mybsdc@qq.com'), config('message.mail.reply_to_name', lang('100074'))); // 备用回复地址，收到的回复的邮件将被发到此地址

        /**
         * 抄送和密送都是添加收件人，抄送方式下，被抄送者知道除被密送者外的所有的收件人，密送方式下，
         * 被密送者知道所有的被抄送者，但不知道其它的被密送者。
         * 抄送好比@，密送好比私信。
         */
//        $this->phpMailerInstance->addCC('cc@example.com'); // 抄送
//        $this->phpMailerInstance->addBCC('bcc@example.com'); // 密送

        // 添加附件，参数2选填
//        $this->phpMailerInstance->addAttachment('README.md', '说明.txt');

        // 标题
        $subject = $subject === '' ? mb_substr($content, 0, 12) . '...' : $subject;
        $this->phpMailerInstance->Subject = $subject;

        // 页脚
        $footer = '';

        /**
         * 正文
         * 使用 html 文件内容作为正文，其中的图片将被 base64 编码，另确保 html 样式为内联形式，且某些样式可能需要 !important 方能正常显示，
         * msgHTML 方法的第二个参数指定 html 内容中图片的路径，在转换时会拼接 html 中图片的相对路径得到完整的路径，最右侧无需“/”，PHPMailer
         * 源码里有加。 css 中的背景图片不会被转换，这是 PHPMailer 已知问题，建议外链
         * 此处也可替换为：
         * $this->phpMailerInstance->isHTML(true); // 设为html格式
         * $this->phpMailerInstance->Body = '正文'; // 支持html
         * $this->phpMailerInstance->AltBody = 'This is an HTML-only message. To view it, activate HTML in your email application.'; // 纯文本消息正文。不支持html预览的邮件客户端将显示此预览消息，其它情况将显示正常的body
         */
        if ($type === 1) {
            $template = file_get_contents($this->noticeTemplatePath);
            $this->setCommonFooter($footer, '<br>', false);
            $message = $this->genMessageContent([
                $content,
                $footer
            ], $template);
        } else if ($type === 2) {
            $template = file_get_contents($this->successfulRenewalTemplatePath);
            $this->setCommonFooter($footer, '<br>', false);
            $realData = [
                $data['username'],
                $data['renewalSuccessArr'] ? sprintf(lang('100075'), $this->genDomainsHtml($data['renewalSuccessArr'])) : '',
                $data['renewalFailuresArr'] ? sprintf(lang('100076'), $this->genDomainsHtml($data['renewalFailuresArr'])) : '',
                $this->genDomainStatusHtml($data['domainStatusArr']),
                $footer
            ];
            $message = $this->genMessageContent($realData, $template);
        } else if ($type === 3) {
            $template = file_get_contents($this->noRenewalRequiredTemplatePath);
            $this->setCommonFooter($footer, '<br>');
            $realData = [
                $data['username'],
                $this->genDomainStatusHtml($data['domainStatusArr']),
                $footer
            ];
            $message = $this->genMessageContent($realData, $template);
        } else if ($type === 4) {
            $template = file_get_contents($this->noticeTemplatePath);
            $this->setCommonFooter($footer, '<br>', false);
            $message = $this->genMessageContent([
                $this->newLine2Br($content),
                $footer
            ], $template);
        } else {
            throw new \Exception(lang('100003'));
        }

        $this->phpMailerInstance->msgHTML($message, RESOURCES_PATH . '/mail/' . $this->language);

        try {
            if (!$this->phpMailerInstance->send()) {
                throw new MailException($this->phpMailerInstance->ErrorInfo);
            }

            return true;
        } catch (\Exception $e) {
            system_log(lang('100077') . $e->getMessage());

            return false;
        }
    }
}
