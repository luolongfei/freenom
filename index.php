<?php
/**
 * 入口文件
 *
 * 云函数版本维护：
 * 1、去掉顶部的 “#!/usr/bin/env php”，将文件名改为 index.php
 * 2、将 “define('IS_SCF', false);” 改为 “define('IS_SCF', true);”
 * 3、干掉最下方的 run(); 调用
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2019/3/2
 * @time 11:05
 * @link https://github.com/luolongfei/freenom
 */

error_reporting(E_ERROR);
ini_set('display_errors', 1);
set_time_limit(0);

define('IS_SCF', true); // 是否云函数环境
define('IS_CLI', PHP_SAPI === 'cli');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', realpath(__DIR__));
define('VENDOR_PATH', realpath(ROOT_PATH . '/vendor'));
define('APP_PATH', realpath(ROOT_PATH . '/app'));
define('DATA_PATH', IS_SCF ? '/tmp' : realpath(ROOT_PATH . '/app/Data')); // 云函数只有 /tmp 目录的读写权限
define('RESOURCES_PATH', realpath(ROOT_PATH . '/resources'));

date_default_timezone_set('Asia/Shanghai');

/**
 * 注册错误处理
 */
register_shutdown_function('customize_error_handler');

/**
 * 注册异常处理
 */
set_exception_handler('exception_handler');

require VENDOR_PATH . '/autoload.php';

use Luolongfei\Libs\Log;
use Luolongfei\Libs\Message;

/**
 * @throws Exception
 */
function customize_error_handler()
{
    if (!is_null($error = error_get_last())) {
        system_log(json_encode($error, JSON_UNESCAPED_UNICODE));
        Log::error(lang('100057'), $error);
        Message::send(lang('100058') . json_encode($error, JSON_UNESCAPED_UNICODE), lang('100059'));
    }
}

/**
 * @param \Exception $e
 *
 * @throws \Exception
 */
function exception_handler($e)
{
    Log::error(lang('100060') . $e->getMessage());
    Message::send(lang('100061') . $e->getMessage(), lang('100062'));
}

/**
 * 腾讯云函数
 *
 * @param $event
 * @param $context
 *
 * @return string
 */
function main_handler($event, $context)
{
    return run();
}

/**
 * 阿里云函数
 *
 * @param $event
 * @param $context
 *
 * @return string
 */
function handler($event, $context)
{
    $logger = $GLOBALS['fcLogger'];
    $logger->info(lang('100063'));

    return run();
}

/**
 * 华为云函数
 *
 * @param $event
 * @param $context
 *
 * @return bool|string
 */
function huawei_handler($event, $context)
{
    $logger = $context->getLogger();

    $logger->info('开始执行华为云函数');

    // 手动设置环境变量
    $logger->info('设置环境变量');
    $allEnvKeys = array_keys((array)env());
    foreach ($allEnvKeys as $key) {
        $value = $context->getUserData((string)$key);
        if (strlen($value) > 0) {
            $logger->info('从控制台发现环境变量：' . $key);
            putenv("{$key}={$value}");
        }
    }
    $logger->info('环境变量设置完成');

    return run();
}

/**
 * @return string|bool
 */
function run()
{
    try {
        system_check();

        $class = sprintf('Luolongfei\App\Console\%s', get_argv('c', 'FreeNom'));
        $fn = get_argv('m', 'handle');

        $class::getInstance()->$fn();

        return IS_SCF ? lang('100007') : true;
    } catch (\Exception $e) {
        system_log(sprintf(lang('100006'), $e->getMessage()), $e->getTrace());
        Message::send(lang('100004') . $e->getMessage(), lang('100005'));
    }

    return IS_SCF ? lang('100008') : false;
}
