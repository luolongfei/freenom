<?php
/**
 * @author luolongf <luolongf@gmail.com>
 * @date 2024-01-25
 * @time 18:22
 */

namespace Luolongfei\App\Console;

class GlobalValue extends Base
{
    /**
     * @var GlobalValue
     */
    private static $instance;

    /**
     * @var array $values
     */
    private $values = [];

    /**
     * @return GlobalValue|self
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function set(string $name, string $value)
    {
        $this->values[$name] = $value;
    }

    /**
     * @param string $name
     * @param string|null $default
     *
     * @return string|null
     */
    public function get(string $name, ?string $default = null)
    {
        return isset($this->values[$name]) ? $this->values[$name] : $default;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function del(string $name)
    {
        unset($this->values[$name]);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name)
    {
        return isset($this->values[$name]);
    }
}
