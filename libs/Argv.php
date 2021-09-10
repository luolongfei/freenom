<?php
/**
 * 命令行参数
 *
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2020/1/3
 * @time 16:32
 */

namespace Luolongfei\Lib;

class Argv
{
    /**
     * @var Argv
     */
    protected static $instance;

    /**
     * @var array 所有命令行传参
     */
    public $allArgvs = [];

    public function __construct()
    {
        if ($this->get('help') || $this->get('h')) {
            $desc = <<<FLL
Description
Params:
-c: <string> 指定要实例化的类名。默认调用FreeNom类
-m: <string> 指定要调用的方法名（不支持静态方法）。默认调用handle方法
-h: 显示说明

Example: 
$ php run -c=FreeNom -m=handle

FLL;
            echo $desc;
            exit(0);
        }
    }

    /**
     * @return Argv
     */
    public static function instance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 获取命令行参数
     *
     * @param string $name
     * @param string $default
     *
     * @return mixed|string
     */
    public function get(string $name, string $default = '')
    {
        if (!$this->allArgvs) {
            $this->setAllArgvs();
        }

        return $this->allArgvs[$name] ?? $default;
    }

    /**
     * 设置命令行所有参数
     *
     * @return array
     */
    public function setAllArgvs()
    {
        global $argv;

        foreach ($argv as $a) { // Windows默认命令行无法正确传入使用引号括住的带空格参数，换个命令行终端就好，Linux不受影响
            if (preg_match('/^-{1,2}(?P<name>\w+)(?:=([\'"]|)(?P<val>[^\n\t\v\f\r\'"]+)\2)?$/i', $a, $m)) {
                $this->allArgvs[$m['name']] = $m['val'] ?? true;
            }
        }

        return $this->allArgvs;
    }
}