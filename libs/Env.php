<?php
/**
 * 环境变量
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2019/6/2
 * @time 17:28
 */

namespace Luolongfei\Libs;

use Dotenv\Dotenv;

class Env extends Base
{
    /**
     * @var array 环境变量值
     */
    protected $allValues = [];

    public function init($fileName = '.env', $overload = false)
    {
        if (file_exists(ROOT_PATH . DS . $fileName)) {
            $this->allValues = $overload ? Dotenv::create(ROOT_PATH, $fileName)->overload() : Dotenv::create(ROOT_PATH, $fileName)->load();
        } else if (IS_SCF) { // 云函数直接从 .env.example 读取默认环境变量
            $fileName = '.env.example';
            if (file_exists(ROOT_PATH . DS . $fileName)) {
                $this->allValues = $overload ? Dotenv::create(ROOT_PATH, $fileName)->overload() : Dotenv::create(ROOT_PATH, $fileName)->load();
            }
        }
    }

    public function get($key = '', $default = null)
    {
        if (!strlen($key)) { // 不传 key 则返回所有环境变量
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