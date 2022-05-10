<?php
/**
 * 配置
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2019/3/3
 * @time 16:41
 */

namespace Luolongfei\Libs;

class Config extends Base
{
    /**
     * @var array 配置
     */
    protected $allConfig;

    protected function init()
    {
        $this->allConfig = require ROOT_PATH . '/config.php';
    }

    /**
     * 获取配置
     *
     * @param string $key
     * @param string $default 默认值
     *
     * @return array|mixed|null
     */
    public function get($key = '', $default = null)
    {
        $allConfig = $this->allConfig;

        if (strlen($key)) {
            if (strpos($key, '.')) {
                $keys = explode('.', $key);
                $val = $allConfig;
                foreach ($keys as $k) {
                    if (!isset($val[$k])) {
                        return $default; // 任一下标不存在就返回默认值
                    }

                    $val = $val[$k];
                }

                return $val;
            } else {
                if (isset($allConfig[$key])) {
                    return $allConfig[$key];
                }

                return $default;
            }
        }

        return $allConfig;
    }
}