<?php
/**
 * 环境变量
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2019/6/2
 * @time 17:28
 */

namespace Luolongfei\Lib;

use Dotenv\Dotenv;

class Env
{
    /**
     * @var Env
     */
    protected static $instance;

    /**
     * @var array 环境变量值
     */
    protected $allValues;

    public function __construct($fileName)
    {
        $this->allValues = Dotenv::create(ROOT_PATH, $fileName)->load();
    }

    public static function instance($fileName = '.env')
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($fileName);
        }

        return self::$instance;
    }

    public function get($key = '', $default = null)
    {
        if (!strlen($key)) { // 不传key则返回所有环境变量
            return $this->allValues;
        }

        $value = getenv($key);
        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') { // 去除双引号
            return substr($value, 1, -1);
        }

        return $value;
    }
}