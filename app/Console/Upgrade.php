<?php
/**
 * 升级
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2021/11/5
 * @time 10:52
 */

namespace Luolongfei\App\Console;

use GuzzleHttp\Client;
use Luolongfei\Libs\Log;
use Luolongfei\Libs\Message;

class Upgrade extends Base
{
    const TIMEOUT = 33;

    /**
     * @var Upgrade
     */
    private static $instance;

    /**
     * @var array 与发布相关的信息
     */
    public $releaseInfo = [];

    /**
     * @var string 最新版本号
     */
    public $latestVer;

    /**
     * @var string 当前版本号
     */
    public $currVer;

    /**
     * @var string 记录已推送版本的文件
     */
    public $pushedVerFile;

    /**
     * @return Upgrade
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->pushedVerFile = DATA_PATH . DS . 'pushed_version.txt';

        $this->client = new Client([
            'base_uri' => 'https://api.github.com',
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json'
            ],
            'cookies' => false,
            'timeout' => self::TIMEOUT,
            'verify' => config('verify_ssl'),
            'debug' => config('debug'),
        ]);
    }

    private function __clone()
    {
    }

    /**
     * 是否需要升级
     *
     * @return bool
     */
    public function needToUpgrade()
    {
        try {
            $resp = $this->client->get('/repos/luolongfei/freenom/releases/latest', [
                'timeout' => 5,
            ]);

            $resp = $resp->getBody()->getContents();
            $resp = (array)json_decode($resp, true);

            if (!isset($resp['tag_name'])
                || !isset($resp['body'])
                || !isset($resp['name'])
                || !isset($resp['published_at'])
                || !isset($resp['html_url'])) {
                throw new \Exception('Github 返回的数据与预期不一致：' . json_encode($resp, JSON_UNESCAPED_UNICODE));
            }

            $this->releaseInfo = $resp;

            $this->latestVer = $this->getVerNum($resp['tag_name']);
            $this->currVer = $this->getVerNum(FreeNom::VERSION);

            return version_compare($this->latestVer, $this->currVer, '>');
        } catch (\Exception $e) {
            Log::error('检测升级出错：' . $e->getMessage());

            return false;
        }
    }

    /**
     * 此版本是否已推送过
     *
     * @param $ver
     *
     * @return bool
     */
    public function isPushed($ver)
    {
        if (!file_exists($this->pushedVerFile)) {
            return false;
        }

        $pushedVerFile = file_get_contents($this->pushedVerFile);

        return stripos($pushedVerFile, $ver) !== false;
    }

    /**
     * 记住版本号
     *
     * @param $ver
     *
     * @return bool
     */
    public function rememberVer($ver)
    {
        return (bool)file_put_contents($this->pushedVerFile, $ver . "\n", FILE_APPEND);
    }

    /**
     * 生成升级送信内容
     *
     * @return string
     */
    public function genMsgContent()
    {
        $content = sprintf(
            "见信好，我们在 %s 发布了新版 FreeNom 续期工具 v%s，而你当前正在使用的版本为 v%s，你可以根据自己的实际需要决定是否升级到新版本。今次新版有以下更新或改进：\n\n",
            $this->friendlyDateFormat($this->releaseInfo['published_at'], 'UTC'),
            $this->latestVer,
            $this->currVer
        );

        $content .= $this->releaseInfo['body'];

        $content .= "\n\n" . '欲知更多信息，请访问：' . $this->releaseInfo['html_url'];

        $content .= "\n\n" . '（本消息针对同一个新版只会推送一次，如果你不想收到新版本通知，将 .env 文件中的 NEW_VERSION_DETECTION 的值设为 0 即可）';

        return $content;
    }

    /**
     * 人类友好时间
     *
     * @param string $date 传入时间
     * @param null|string $timezone 传入时间的时区
     *
     * @return string
     */
    public function friendlyDateFormat($date, $timezone = null)
    {
        try {
            $d = (new \DateTime($date, $timezone ? new \DateTimeZone($timezone) : null))->setTimezone(new \DateTimeZone('Asia/Shanghai'));

            $time = $d->getTimestamp();
            $diff = time() - $time;

            if ($diff < 86400) {
                if ($d->format('d') === date('d')) {
                    return $diff < 60 ? '刚刚' : ($diff < 3600 ? floor($diff / 60) . '分钟前' : floor($diff / 3600) . '小时前');
                } else {
                    return '昨天 ' . $d->format('H:i');
                }
            } else {
                return $d->format($d->format('Y') === date('Y') ? 'Y-m-d H:i' : 'Y-m-d');
            }
        } catch (\Exception $e) {
            Log::error('转人类友好时间出错：' . $e->getMessage());

            return $date;
        }
    }

    /**
     * @return bool
     */
    public function handle()
    {
        try {
            if (!$this->needToUpgrade()) {
                return true;
            }

            if ($this->isPushed($this->latestVer)) {
                return true;
            }

            if (IS_SCF) {
                system_log(sprintf(
                    'FreeNom 续期工具有新的版本可用，你当前版本为 v%s，最新版本为 v%s。关于新版的详细信息，请访问：%s',
                    $this->currVer,
                    $this->latestVer,
                    $this->releaseInfo['html_url']
                ));
            } else {
                system_log(sprintf(
                    '<green>FreeNom 续期工具有新的版本可用，最新版本为 v%s（%s）</green>',
                    $this->latestVer,
                    $this->releaseInfo['html_url']
                ));

                $result = Message::send(
                    $this->genMsgContent(),
                    sprintf('主人，FreeNom 续期工具有新的版本（v%s）可用，新版相关情况已给到你', $this->latestVer),
                    4,
                    $this->releaseInfo
                );

                if ($result) {
                    $this->rememberVer($this->latestVer);
                    system_log('有关新版的信息已送信给到你，请注意查收。');
                }
            }

            return true;
        } catch (\Exception $e) {
            system_log('升级出错：' . $e->getMessage());

            return false;
        }
    }

    public function doUpgrade()
    {
        // TODO 自动升级
//        system_log('<green>恭喜，已完成升级。</green>');
    }
}
