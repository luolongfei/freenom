<?php
/**
 * 助手函数
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2019/3/3
 * @time 16:34
 */

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Luolongfei\App\Console\GlobalValue;
use Luolongfei\App\Console\MigrateEnvFile;
use Luolongfei\App\Console\Upgrade;
use Luolongfei\App\Constants\CommonConst;
use Luolongfei\App\Exceptions\LlfException;
use Luolongfei\Libs\Argv;
use Luolongfei\Libs\Config;
use Luolongfei\Libs\Env;
use Luolongfei\Libs\Lang;
use Luolongfei\Libs\Log;
use Luolongfei\Libs\PhpColor;

if (!function_exists('config')) {
    /**
     * 获取配置
     *
     * @param string $key 键，支持点式访问
     * @param string $default 默认值
     *
     * @return array|mixed
     */
    function config($key = '', $default = null)
    {
        return Config::getInstance()->get($key, $default);
    }
}

if (!function_exists('lang')) {
    /**
     * 读取语言包
     *
     * @param string $key 键，支持点式访问
     *
     * @return array|mixed|null
     */
    function lang($key = '')
    {
        return Lang::getInstance()->get($key);
    }
}

if (!function_exists('system_log')) {
    /**
     * 写日志
     *
     * @param $content
     * @param array $response
     * @param string $fileName
     * @description 受支持的着色标签
     * 'reset', 'bold', 'dark', 'italic', 'underline', 'blink', 'reverse', 'concealed', 'default', 'black', 'red',
     * 'green', 'yellow', 'blue', 'magenta', 'cyan', 'light_gray', 'dark_gray', 'light_red', 'light_green',
     * 'light_yellow', 'light_blue', 'light_magenta', 'light_cyan', 'white', 'bg_default', 'bg_black', 'bg_red',
     * 'bg_green', 'bg_yellow', 'bg_blue', 'bg_magenta', 'bg_cyan', 'bg_light_gray', 'bg_dark_gray', 'bg_light_red',
     * 'bg_light_green','bg_light_yellow', 'bg_light_blue', 'bg_light_magenta', 'bg_light_cyan', 'bg_white'
     *
     * system_log('<light_magenta>颜色 light_magenta</light_magenta>');
     */
    function system_log($content, array $response = [], $fileName = '')
    {
        try {
            $msg = sprintf(
                "[%s] %s %s\n",
                date('Y-m-d H:i:s'),
                is_string($content) ? $content : json_encode($content),
                $response ? json_encode($response, JSON_UNESCAPED_UNICODE) : '');

            // 过滤敏感信息
            if ((int)env('MOSAIC_SENSITIVE_INFO') === 1) {
                // 在 php 7.3 之前，连字符“-”在中括号中随便放，但在之后，只能放在开头或结尾或者转义后才能随便放
                $msg = preg_replace_callback('/(?P<secret>[\w.-]{1,3}?)(?=@[\w.-]+)/ui', function ($m) {
                    return str_ireplace($m['secret'], str_repeat('*', strlen($m['secret'])), $m['secret']);
                }, $msg);
            }

            // 尝试为消息着色
            $c = PhpColor::getInstance()->getColorInstance();
            echo $c($msg)->colorize();

            // 干掉着色标签
            $msg = strip_tags($msg); // 不完整或者破损标签将导致更多的数据被删除

            // 写入日志文件
            if (is_writable(ROOT_PATH)) {
                $path = sprintf('%s/logs/%s/', ROOT_PATH, date('Y-m'));
                $file = $path . ($fileName ?: date('d')) . '.log';

                if (!is_dir($path)) {
                    mkdir($path, 0666, true); // 0666 所有用户可读写
                }

                $handle = fopen($file, 'a'); // 追加而非覆盖

                if ($handle !== false) {
                    if (!filesize($file)) {
                        chmod($file, 0666);
                    }

                    fwrite($handle, $msg);
                    fclose($handle);
                }
            }

            flush();
        } catch (\Exception $e) {
            // do nothing
        }
    }
}

if (!function_exists('is_locked')) {
    /**
     * 检查任务是否已被锁定
     *
     * @param string $taskName
     * @param bool $always 是否被永久锁定
     *
     * @return bool
     * @throws Exception
     */
    function is_locked($taskName = '', $always = false)
    {
        try {
            $lock = sprintf(
                '%s/num_limit/%s/%s.lock',
                APP_PATH,
                $always ? 'always' : date('Y-m-d'),
                $taskName
            );

            return file_exists($lock);
        } catch (\Exception $e) {
            system_log(sprintf('检查任务%s是否锁定时出错，错误原因：%s', $taskName, $e->getMessage()));
        }

        return false;
    }
}

if (!function_exists('lock_task')) {
    /**
     * 锁定任务
     *
     * 防止重复执行
     *
     * @param string $taskName
     * @param bool $always 是否永久锁定
     *
     * @return bool
     */
    function lock_task($taskName = '', $always = false)
    {
        try {
            $lock = sprintf(
                '%s/num_limit/%s/%s.lock',
                APP_PATH,
                $always ? 'always' : date('Y-m-d'),
                $taskName
            );

            $path = dirname($lock);
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
                chmod($path, 0777);
            }

            if (file_exists($lock)) {
                return true;
            }

            $handle = fopen($lock, 'a'); // 追加而非覆盖

            if (!filesize($lock)) {
                chmod($lock, 0666);
            }

            fwrite($handle, sprintf(
                    "Locked at %s.\n",
                    date('Y-m-d H:i:s')
                )
            );

            fclose($handle);

            Log::info(sprintf('%s已被锁定，此任务%s已不会再执行，请知悉', $taskName, $always ? '' : '今天内'));
        } catch (\Exception $e) {
            system_log(sprintf('创建锁定任务文件%s时出错，错误原因：%s', $lock, $e->getMessage()));

            return false;
        }

        return true;
    }
}

if (!function_exists('env')) {
    /**
     * 获取环境变量值
     *
     * @param string $key
     * @param string $default 默认值
     *
     * @return array | bool | false | null | string
     */
    function env($key = '', $default = null)
    {
        return Env::getInstance()->get($key, $default);
    }
}

if (!function_exists('get_argv')) {
    /**
     * 获取命令行传参
     *
     * @param string $name
     * @param string $default 默认值
     *
     * @return mixed|string
     */
    function get_argv(string $name, string $default = '')
    {
        return Argv::getInstance()->get($name, $default);
    }
}

if (!function_exists('system_check')) {
    /**
     * 检查环境是否满足要求
     *
     * @throws LlfException
     */
    function system_check()
    {
        // 由于各种云函数目前支持的最大的 PHP 版本为 7.2，故此处暂时不强制要求升级 PHP 7.3 以获得更好的兼容性
        if (version_compare(PHP_VERSION, '7.2.0') < 0) {
            throw new LlfException(34520006, ['7.3', PHP_VERSION]);
        }

        // 特殊环境无需检查这几项
        if (IS_SCF || !is_writable(ROOT_PATH) || (int)env('IS_KOYEB') === 1 || (int)env('IS_HEROKU') === 1) {
            system_log(lang('100009'));
        } else {
            if (!function_exists('putenv')) {
                throw new LlfException(34520005);
            }

            $envFile = ROOT_PATH . '/.env';
            if (!file_exists($envFile)) {
                throw new LlfException(copy(ROOT_PATH . '/.env.example', $envFile) ? 34520007 : 34520008);
            }

            // 检查当前 .env 文件版本是否过低，过低自动升级
            MigrateEnvFile::getInstance()->handle();
        }

        // 是否有新版可用
        if (config('new_version_detection')) {
            Upgrade::getInstance()->handle();
        } else {
            system_log(lang('100012'));
        }

        if (!extension_loaded('curl')) {
            throw new LlfException(34520010);
        }
    }
}

if (!function_exists('get_local_num')) {
    /**
     * 获取当地数字
     *
     * @param string|int $num
     *
     * @return string
     */
    function get_local_num($num)
    {
        $num = (string)$num;

        if (is_chinese()) {
            return $num;
        }

        // 英文数字规则
        $lastDigit = substr($num, -1);
        switch ($lastDigit) {
            case '1':
                return $num . 'st';
            case '2':
                return $num . 'nd';
            case '3':
                return $num . 'rd';
            default:
                return $num . 'th';
        }
    }
}

if (!function_exists('is_chinese')) {
    /**
     * 判断当前语言环境
     *
     * @return bool
     */
    function is_chinese()
    {
        return config('custom_language', 'zh') === 'zh';
    }
}

if (!function_exists('get_ip_info')) {
    /**
     * 获取 ip 信息
     *
     * @return string
     */
    function get_ip_info()
    {
        return \Luolongfei\Libs\IP::getInstance()->get();
    }
}

if (!function_exists('get_random_user_agent')) {
    /**
     * 获取随机 user-agent
     *
     * @return string
     */
    function get_random_user_agent()
    {
        $chromeVersions = [
            '121.0.0.0',
        ];

        return $chromeVersions[array_rand($chromeVersions)];
    }
}

if (!function_exists('autoRetry')) {
    /**
     * 自动重试
     *
     * @param $func
     * @param int $maxRetryCount
     * @param array $params
     *
     * @return mixed|void
     * @throws Exception
     */
    function autoRetry($func, $maxRetryCount = 3, $params = [])
    {
        $retryCount = 0;
        while (true) {
            try {
                return call_user_func_array($func, $params);
            } catch (\Exception $e) {
                $retryCount++;
                if ($retryCount > $maxRetryCount) {
                    throw $e;
                }

                $sleepTime = getSleepTime($retryCount, 2, 10);

                if (stripos($e->getMessage(), '405') !== false) {
                    system_log(\lang('100141'));

                    sleep(9);

                    // aws waf token 失效，将重新获取新的 token
                    $handleInvalidToken = false;
                    foreach ($params as &$param) {
                        if ($param instanceof CookieJar) {
                            $handleInvalidToken = true;
                            $sleepTime = 1;
                            delGlobalValue(CommonConst::AWS_WAF_TOKEN);
                            $param->setCookie(buildAwsWafCookie(getAwsWafToken()));

                            break;
                        }
                    }

                    system_log($handleInvalidToken ? \lang('exception_msg.34520019') : sprintf(lang('exception_msg.34520015'), $sleepTime, $maxRetryCount, $retryCount, $maxRetryCount));

                    continue;
                } else {
                    system_log(sprintf(lang('exception_msg.34520016'), $e->getMessage(), $sleepTime, $maxRetryCount, $retryCount, $maxRetryCount));
                }

                sleep($sleepTime);
            }
        }
    }
}

if (!function_exists('buildAwsWafCookie')) {
    /**
     * 构建 aws waf cookie
     *
     * @param string $awsWafToken
     *
     * @return SetCookie
     */
    function buildAwsWafCookie(string $awsWafToken)
    {
        $cookie = new SetCookie();

        $cookie->setName('aws-waf-token');
        $cookie->setValue($awsWafToken);
        $cookie->setDomain('.my.freenom.com');

        return $cookie;
    }
}

if (!function_exists('getSleepTime')) {
    /**
     * 获取睡眠秒数
     *
     * @param int $i
     * @param int $magRatio
     * @param int $minSleepTime
     *
     * @return int
     */
    function getSleepTime($i, $magRatio = 4, $minSleepTime = 20)
    {
        $sleepTime = $i * $magRatio;
        if ($sleepTime < $minSleepTime) { // 最小休眠 $minSleepTime 秒
            return $minSleepTime;
        }

        return $sleepTime;
    }
}

if (!function_exists('getAwsWafToken')) {
    /**
     * 获取 aws waf token
     *
     * @return string
     * @throws LlfException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function getAwsWafToken()
    {
        // 优先从全局变量中获取
        $AWS_WAF_TOKEN = getGlobalValue(CommonConst::AWS_WAF_TOKEN);
        if ($AWS_WAF_TOKEN !== null) {
            return $AWS_WAF_TOKEN;
        }

        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => \env('FF_SECRET_KEY', '')
            ],
            'timeout' => 32,
        ]);

        // 调用开源的接口获取
        $USE_OPEN_SOURCE_WAF_SOLVER_API = (int)\env('USE_OPEN_SOURCE_WAF_SOLVER_API', 1);
        if ($USE_OPEN_SOURCE_WAF_SOLVER_API === 1) {
            $OPEN_SOURCE_WAF_SOLVER_URL = \env('OPEN_SOURCE_WAF_SOLVER_URL');
            if (!$OPEN_SOURCE_WAF_SOLVER_URL) {
                throw new LlfException(34520020);
            }
            $OPEN_SOURCE_WAF_SOLVER_URL = rtrim($OPEN_SOURCE_WAF_SOLVER_URL, '/');

            $startTime = time();
            $maxWaitSeconds = 300;
            $n = 0;
            while (true) {
                try {
                    if (time() - $startTime >= $maxWaitSeconds) {
                        break;
                    }

                    $r = $client->get($OPEN_SOURCE_WAF_SOLVER_URL);
                    $body = json_decode($r->getBody()->getContents(), true);

                    if (!isset($body['status']) || $body['status'] !== 'OK') {
                        throw new \Exception(isset($body['msg']) ? $body['msg'] : json_encode($body));
                    }

                    $awsWafToken = $body['data']['token'];
                    setGlobalValue(CommonConst::AWS_WAF_TOKEN, $awsWafToken);

                    system_log(sprintf(lang('100139'), $awsWafToken));

                    return $awsWafToken;
                } catch (\Exception $e) {
                    system_log('<red>getAwsWafToken error:</red> ' . $e->getMessage());
                }

                $n++;

                sleep($n > 5 ? 60 : 10); // 前 5 次每次休眠 10 秒，之后每次休眠 60 秒
            }

            throw new LlfException(34520021, $maxWaitSeconds);
        }

        // 使用自建接口获取
        $AWS_WAF_SOLVER_URL = \env('AWS_WAF_SOLVER_URL');
        if (!$AWS_WAF_SOLVER_URL) {
            throw new LlfException(34520017);
        }
        $AWS_WAF_SOLVER_URL = rtrim($AWS_WAF_SOLVER_URL, '/');

        $i = 0;
        do {
            try {
                // 获取任务 ID
                $r = $client->get($AWS_WAF_SOLVER_URL);
                $body = json_decode($r->getBody()->getContents(), true);

                if (!isset($body['status']) || $body['status'] !== 'OK') {
                    // 一般情况下走不到这个分支
                    if (isset($body['msg']) && $body['msg'] === 'A task is already running') {
                        sleep(180);
                    }

                    throw new \Exception(isset($body['msg']) ? $body['msg'] : json_encode($body));
                }

                // 已获取任务 ID，等待任务完成
                $taskId = $body['data']['task_id'];
                $startTime = time();

                while (true) {
                    // 最多等 10 分钟
                    if (time() - $startTime >= 600) {
                        break;
                    }

                    $r = $client->get(sprintf('%s/%s', $AWS_WAF_SOLVER_URL, $taskId));
                    $body = json_decode($r->getBody()->getContents(), true);

                    if (!isset($body['status']) || $body['status'] !== 'OK') {
                        throw new \Exception(isset($body['msg']) ? $body['msg'] : json_encode($body));
                    }

                    $taskStatus = $body['data']['task_status'];
                    if ($taskStatus !== 'done') { // 任务进行中，继续等待
                        sleep(3);

                        continue;
                    }

                    if (!isset($body['data']['result']) || $body['data']['result'] === '') {
                        throw new \Exception('no result');
                    }

                    $awsWafToken = $body['data']['result'] ?? '';
                    setGlobalValue(CommonConst::AWS_WAF_TOKEN, $awsWafToken);

                    system_log(sprintf(lang('100139'), $awsWafToken));

                    return $awsWafToken;
                }
            } catch (\Exception $e) {
                system_log('<red>getAwsWafToken error:</red> ' . $e->getMessage());
            }

            sleep(1);

            $i++;
        } while ($i <= 10);

        throw new LlfException('34520018');
    }
}

if (!function_exists('getGlobalValue')) {
    /**
     * 获取全局变量
     *
     * @param string $name
     *
     * @return string|null
     */
    function getGlobalValue(string $name, ?string $default = null)
    {
        return GlobalValue::getInstance()->get($name, $default);
    }
}

if (!function_exists('setGlobalValue')) {
    /**
     * 设置全局变量
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    function setGlobalValue(string $name, string $value)
    {
        GlobalValue::getInstance()->set($name, $value);
    }
}

if (!function_exists('hasGlobalValue')) {
    /**
     * 是否存在全局变量
     *
     * @param string $name
     *
     * @return bool
     */
    function hasGlobalValue(string $name)
    {
        return GlobalValue::getInstance()->has($name);
    }
}

if (!function_exists('delGlobalValue')) {
    /**
     * 删除全局变量
     *
     * @param string $name
     *
     * @return void
     */
    function delGlobalValue(string $name)
    {
        GlobalValue::getInstance()->del($name);
    }
}

if (!function_exists('needAwsWafToken')) {
    /**
     * @return bool
     */
    function needAwsWafToken()
    {
        try {
            $client = new Client([
                'headers' => [
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
                ],
                'timeout' => 6.2011,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_AUTOREFERER => true,
                'verify' => config('verify_ssl'),
                'proxy' => config('freenom_proxy'),
            ]);
            $res = $client->get('https://my.freenom.com/clientarea.php');

            return $res->getStatusCode() != 200;
        } catch (\Exception $e) {
            return stripos($e->getMessage(), '405') !== false;
        }
    }
}
