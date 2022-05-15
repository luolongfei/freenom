<?php
/**
 * Bark 推送
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2021/11/3
 * @time 11:18
 */

namespace Luolongfei\Libs\MessageServices;

use GuzzleHttp\Client;
use Luolongfei\Libs\Connector\MessageGateway;

class Bark extends MessageGateway
{
    const TIMEOUT = 33;

    /**
     * @var string Bark Key
     */
    protected $barkKey;

    /**
     * @var string Bark 域名
     */
    protected $barkUrl;

    /**
     * @var integer|string 指定是否需要保存推送信息到历史记录，1 为保存，其他值为不保存。如果值为空字符串，则推送信息将按照 APP 内设置来决定是否保存
     */
    protected $isArchive;

    /**
     * @var string 指定推送消息分组，可在历史记录中按分组查看推送
     */
    protected $group;

    /**
     * 可选参数值
     * active：不设置时的默认值，系统会立即亮屏显示通知
     * timeSensitive：时效性通知，可在专注状态下显示通知
     * passive：仅将通知添加到通知列表，不会亮屏提醒
     *
     * @var string 时效性通知
     */
    protected $level;

    /**
     * @var string 指定推送消息图标 (仅 iOS15 或以上支持）http://day.app/assets/images/avatar.jpg
     */
    protected $icon;

    /**
     * @var string 点击推送将跳转到url的地址（发送时，URL参数需要编码），GuzzleHttp 库会自动编码
     */
    protected $jumpUrl;

    /**
     * IOS14.5 之后长按或下拉推送即可触发自动复制，IOS14.5 之前无需任何操作即可自动复制
     *
     * @var integer 携带参数 automaticallyCopy=1， 收到推送时，推送内容会自动复制到粘贴板（如发现不能自动复制，可尝试重启一下手机）
     */
    protected $automaticallyCopy = 1;

    /**
     * @var string 携带 copy 参数， 则上面两种复制操作，将只复制 copy 参数的值
     */
    protected $copy = 'https://my.freenom.com/domains.php?a=renewals';

    /**
     * @var string 通知铃声
     */
    protected $sound;

    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $this->barkKey = $this->parseBarkKey(config('message.bark.bark_key'));
        $this->barkUrl = rtrim(config('message.bark.bark_url'), '/');

        $this->isArchive = config('message.bark.bark_is_archive');
        $this->group = config('message.bark.bark_group');
        $this->level = config('message.bark.bark_level');
        $this->icon = config('message.bark.bark_icon');
        $this->jumpUrl = config('message.bark.bark_jump_url');
        $this->sound = config('message.bark.bark_sound');

        $this->client = new Client([
            'cookies' => false,
            'timeout' => self::TIMEOUT,
            'verify' => config('verify_ssl'),
            'debug' => config('debug'),
        ]);
    }

    /**
     * 解析 Bark Key
     *
     * 支持从这类 url 地址中提取 Bark Key
     * https://api.day.app/xxx/这里改成你自己的推送内容
     *
     * @param string $barkKey
     *
     * @return string
     */
    public function parseBarkKey(string $barkKey)
    {
        if (preg_match('/^https?:\/\/[^\/]+?\/(?P<barkKey>.+?)\//iu', $barkKey, $m)) {
            return $m['barkKey'];
        }

        return $barkKey;
    }

    /**
     * 生成域名文本
     *
     * @param array $domains
     *
     * @return string
     */
    public function genDomainsText(array $domains)
    {
        $domainsText = '';

        foreach ($domains as $domain) {
            $domainsText .= sprintf('%s ', $domain);
        }

        $domainsText = trim($domainsText, ' ') . "\n";

        return $domainsText;
    }

    /**
     * 获取页脚
     *
     * @return string
     */
    public function getFooter()
    {
        $footer = '';

        $footer .= lang('100078');

        return $footer;
    }

    /**
     * 生成域名状态文本
     *
     * @param array $domainStatus
     *
     * @return string
     */
    public function genDomainStatusText(array $domainStatus)
    {
        if (empty($domainStatus)) {
            return lang('100080');
        }

        $domainStatusText = '';

        foreach ($domainStatus as $domain => $daysLeft) {
            $domainStatusText .= sprintf(lang('100081'), $domain, $daysLeft);
        }

        $domainStatusText = rtrim(rtrim($domainStatusText, ' '), '，,') . lang('100082');

        return $domainStatusText;
    }

    /**
     * 生成域名续期结果文本
     *
     * @param string $username
     * @param array $renewalSuccessArr
     * @param array $renewalFailuresArr
     * @param array $domainStatus
     *
     * @return string
     */
    public function genDomainRenewalResultsText(string $username, array $renewalSuccessArr, array $renewalFailuresArr, array $domainStatus)
    {
        $text = sprintf(lang('100083'), $username);

        if ($renewalSuccessArr) {
            $text .= lang('100084');
            $text .= $this->genDomainsText($renewalSuccessArr);
        }

        if ($renewalFailuresArr) {
            $text .= lang('100085');
            $text .= $this->genDomainsText($renewalFailuresArr);
        }

        $text .= lang('100086');
        $text .= $this->genDomainStatusText($domainStatus);

        $text .= $this->getFooter();

        return $text;
    }

    /**
     * 生成域名状态完整文本
     *
     * @param string $username
     * @param array $domainStatus
     *
     * @return string
     */
    public function genDomainStatusFullText(string $username, array $domainStatus)
    {
        $markDownText = sprintf(lang('100087'), $username);

        $markDownText .= $this->genDomainStatusText($domainStatus);

        $markDownText .= $this->getFooter();

        return $markDownText;
    }

    /**
     * 送信
     *
     * @param string $content
     * @param string $subject
     * @param int $type
     * @param array $data
     * @param string|null $recipient
     * @param mixed ...$params
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function send(string $content, string $subject = '', int $type = 1, array $data = [], ?string $recipient = null, ...$params)
    {
        $this->check($content, $data);

        $commonFooter = '';

        if ($type === 1 || $type === 4) {
            $this->setCommonFooter($commonFooter, "\n", false);
        } else if ($type === 2) {
            $this->setCommonFooter($commonFooter, "\n", false);
            $content = $this->genDomainRenewalResultsText($data['username'], $data['renewalSuccessArr'], $data['renewalFailuresArr'], $data['domainStatusArr']);
        } else if ($type === 3) {
            $this->setCommonFooter($commonFooter);
            $content = $this->genDomainStatusFullText($data['username'], $data['domainStatusArr']);
        } else {
            throw new \Exception(lang('100003'));
        }

        $content .= $commonFooter;

        $query = [
            'level' => $this->level,
            'automaticallyCopy' => $this->automaticallyCopy, // 携带参数 automaticallyCopy=1， 收到推送时，推送内容会自动复制到粘贴板（如发现不能自动复制，可尝试重启一下手机）
            'copy' => isset($data['html_url']) ? $data['html_url'] : $this->copy, // 携带 copy 参数，则上面的复制操作，将只复制 copy 参数的值
        ];

        if ($this->isArchive !== null) {
            $query['isArchive'] = $this->isArchive;
        }
        if ($this->group !== null) {
            $query['group'] = $this->group;
        }
        if ($this->icon !== null) {
            $query['icon'] = $this->icon;
        }
        if ($this->jumpUrl !== null) {
            $query['url'] = $this->jumpUrl;
        }
        if ($this->sound !== null) {
            $query['sound'] = $this->sound;
        }
        if (isset($data['badge'])) { // 设置角标
            $query['badge'] = $data['badge'];
        }

        $formParams = [
            'body' => $content, // 推送内容 换行请使用换行符 \n
        ];

        if ($subject !== '') {
            $formParams['title'] = $subject; // 推送标题 比 body 字号粗一点
        }

        try {
            $resp = $this->client->post(
                sprintf('%s/%s/', $this->barkUrl, $this->barkKey),
                [
                    'query' => $query,
                    'form_params' => $formParams,
                ]
            );

            $resp = json_decode($resp->getBody()->getContents(), true);

            if (isset($resp['code']) && $resp['code'] === 200) {
                return true;
            }

            throw new \Exception($resp['message'] ?? lang('100088'));
        } catch (\Exception $e) {
            system_log(sprintf(lang('100089'), $e->getMessage()));

            return false;
        }
    }
}