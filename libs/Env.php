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

    public function init($filename = '.env', $overload = true)
    {
        if (file_exists(ROOT_PATH . DS . $filename)) {
            $this->setAllValues($filename, $overload);
        } else { // 云函数或 Heroku 或 Railway 直接从 .env.example 读取默认环境变量
            $this->setAllValues('.env.example', $overload);
        }
    }

    /**
     * 读取并设置所有环境变量
     *
     * @param $filename
     * @param $overload
     *
     * @return void
     */
    private function setAllValues($filename, $overload)
    {
        $this->allValues = $overload ? Dotenv::create(ROOT_PATH, $filename)->overload() : Dotenv::create(ROOT_PATH, $filename)->load();
    }

    /**
     * 获取环境变量
     *
     * @param $key
     * @param $default
     *
     * @return array|bool|mixed|string|null
     */
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