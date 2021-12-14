<?php
/**
 * 助手函数
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2019/3/3
 * @time 16:34
 */

use Luolongfei\App\Exceptions\LlfException;
use Luolongfei\Libs\Argv;
use Luolongfei\Libs\Config;
use Luolongfei\Libs\Log;
use Luolongfei\Libs\Env;
use Luolongfei\Libs\Lang;
use Luolongfei\Libs\PhpColor;
use Luolongfei\App\Console\MigrateEnvFile;
use Luolongfei\App\Console\Upgrade;

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
     */
    function system_log($content, array $response = [], $fileName = '')
    {
        try {
            $path = sprintf('%s/logs/%s/', ROOT_PATH, date('Y-m'));
            $file = $path . ($fileName ?: date('d')) . '.log';

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
                chmod($path, 0777);
            }

            $handle = fopen($file, 'a'); // 追加而非覆盖

            if (!filesize($file)) {
                chmod($file, 0666);
            }

            $msg = sprintf(
                "[%s] %s %s\n",
                date('Y-m-d H:i:s'),
                is_string($content) ? $content : json_encode($content),
                $response ? json_encode($response, JSON_UNESCAPED_UNICODE) : '');

            // 在 Github Actions 上运行，过滤敏感信息
            if (env('ON_GITHUB_ACTIONS')) {
                $msg = preg_replace_callback('/(?P<secret>[\w-.]{1,4}?)(?=@[\w-.]+)/i', function ($m) {
                    return str_ireplace($m['secret'], str_repeat('*', strlen($m['secret'])), $m['secret']);
                }, $msg);
            }

            // 尝试为消息着色
            $c = PhpColor::getInstance()->getColorInstance();
            echo $c($msg)->colorize();

            // 干掉着色标签
            $msg = strip_tags($msg); // 不完整或者破损标签将导致更多的数据被删除

            fwrite($handle, $msg);
            fclose($handle);

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
        if (version_compare(PHP_VERSION, '7.0.0') < 0) {
            throw new LlfException(34520006);
        }

        // 如果是在云函数部署，则不需要检查这几项
        if (IS_SCF) {
            system_log('检测到运行环境为云函数，所有环境变量将直接从环境中读取，环境中找不到的变量，则直接从 .env.example 文件中读取');
            system_log('如果是在腾讯云函数，可以参考此处修改或新增环境变量，无需重建：https://github.com/luolongfei/freenom/blob/main/resources/screenshot/scf03.png');
            system_log('如果是在阿里云函数，可以直接在【函数详情】->【函数配置】->【环境信息】处编辑环境变量');
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
            system_log('由于你没有开启升级提醒功能，故无法在有新版本可用时第一时间收到通知。将 .env 文件中 NEW_VERSION_DETECTION 的值改为 1 即可重新开启相关功能。');
        }

        if (!extension_loaded('curl')) {
            throw new LlfException(34520010);
        }
    }
}
