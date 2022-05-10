<?php
/**
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2021/10/22
 * @time 17:13
 */

namespace Luolongfei\Libs;

class Base
{
    /**
     * @var array
     */
    private static $instances = [];

    /**
     * 添加单例
     *
     * @param string $className
     * @param bool $overwrite
     *
     * @throws \Exception
     */
    public static function addInstance(string $className, bool $overwrite = false)
    {
        if (isset(self::$instances[$className]) && !$overwrite) {
            throw new \InvalidArgumentException(sprintf(lang('100053'), $className));
        }

        if (!class_exists($className)) {
            throw new \Exception(sprintf(lang('100054'), $className));
        }

        $instance = new $className();

        self::$instances[$className] = $instance;
    }

    /**
     * 获取对象实例
     *
     * @param ...$params
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getInstance(...$params)
    {
        $className = isset($params[1]) && $params[1] === 'IS_MESSAGE_SERVICE' ? $params[0] : static::class;

        if (!isset(self::$instances[$className])) {
            self::addInstance($className);

            // 由于自 php8 开始，is_callable 函数中如果使用类名，将不再适用于非静态方法，非静态方法必须使用对象实例，故只能将 init 从基类的
            // 普通构造函数迁移至此处，既可以实现单次调用非静态初始化方法，又不影响继承
            if (is_callable([self::$instances[$className], 'init'])) {
                self::$instances[$className]->init(...$params);
            }
        }

        return self::$instances[$className];
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
