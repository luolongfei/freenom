<?php
declare(strict_types=1);

if (!defined('IS_SCF')) {
    define('IS_SCF', false);
}

if (!defined('IS_CLI')) {
    define('IS_CLI', true);
}

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

if (!defined('VENDOR_PATH')) {
    define('VENDOR_PATH', ROOT_PATH . DS . 'vendor');
}

if (!defined('APP_PATH')) {
    define('APP_PATH', ROOT_PATH . DS . 'app');
}

if (!defined('DATA_PATH')) {
    define('DATA_PATH', ROOT_PATH . DS . 'tests' . DS . 'runtime' . DS . 'data');
}

if (!defined('RESOURCES_PATH')) {
    define('RESOURCES_PATH', ROOT_PATH . DS . 'resources');
}

date_default_timezone_set('Asia/Shanghai');

$runtimeDirs = [
    ROOT_PATH . DS . 'tests' . DS . 'runtime',
    DATA_PATH,
];

foreach ($runtimeDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

require VENDOR_PATH . DS . 'autoload.php';
