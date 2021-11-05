<?php
/**
 * 基类
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2021/11/3
 * @time 16:32
 */

namespace Luolongfei\App\Console;

class Base
{
    /**
     * 获取版本号数字部分
     *
     * @param $rawVer
     *
     * @return string|null
     */
    public function getVerNum($rawVer)
    {
        if (preg_match('/(?P<ver_num>\d+(?:\.\d+)*)/i', $rawVer, $m)) {
            return $m['ver_num'];
        }

        return null;
    }
}
