<?php
/**
 * 语言包加载
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2020/1/16
 * @time 16:30
 */

namespace Luolongfei\Libs;

class Lang extends Base
{
    /**
     * @var array
     */
    public $lang;

    public function init()
    {
        // 读取语言包，语言包位于 resources/lang/ 目录下
        $this->lang = require sprintf('%s/lang/%s.php', RESOURCES_PATH, strtolower(env('CUSTOM_LANGUAGE', 'zh')));
    }

    /**
     * @param string $key
     *
     * @return array|mixed|null
     */
    public function get($key = '')
    {
        $lang = $this->lang;

        if (strlen($key)) {
            if (strpos($key, '.')) {
                $keys = explode('.', $key);
                $val = $lang;
                foreach ($keys as $k) {
                    if (!isset($val[$k])) {
                        return null; // 任一下标不存在就返回null
                    }

                    $val = $val[$k];
                }

                return $val;
            } else {
                if (isset($lang[$key])) {
                    return $lang[$key];
                } else if (isset($lang['messages'][$key])) { // 如果没有在根节点找到语言数据，则尝试从 messages 下标继续找寻
                    return $lang['messages'][$key];
                }

                return null;
            }
        }

        return $lang;
    }
}