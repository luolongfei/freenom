<?php
/**
 * PHP命令行颜色
 *
 * 目前多层标签嵌套还存在问题，当多层嵌套时，内嵌标签后面的文字样式会丢失，可暂时使用<reset>标签恢复，待原作者修正
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2019/11/29
 * @time 10:52
 */

namespace Luolongfei\Lib;

use Colors\Color;

class PhpColor
{
    /**
     * @var PhpColor
     */
    protected static $instance;

    /**
     * @var Color
     */
    protected $colorInstance;

    public function __construct()
    {
        $this->colorInstance = new Color();

        // Create my own style
        $this->colorInstance->setUserStyles([
//                '自定义标签' => 'red',
        ]);
    }

    /**
     * @return PhpColor
     */
    public static function instance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return Color
     */
    public function getColorInstance()
    {
        return $this->colorInstance;
    }
}