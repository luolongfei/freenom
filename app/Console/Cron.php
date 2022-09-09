<?php
/**
 * @author mybsdc <mybsdc@gmail.com>
 * @date 2021/11/17
 * @time 11:23
 */

namespace Luolongfei\App\Console;

class Cron extends Base
{
    /**
     * @var Cron
     */
    private static $instance;

    /**
     * @return Cron
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
     * 验证 Cron 表达式是否合法
     */
    public function verify()
    {
        $cronExp = get_argv('cron_exp');

        if (preg_match('/^(?:\*(?:\/\d+)?|[0-5]?\d(?:,[0-5]?\d)*|[0-5]?\d-[0-5]?\d(?:\/\d+)?) (?:\*(?:\/\d+)?|(?:\d|0\d|1\d|2[0-3])(?:,(?:\d|0\d|1\d|2[0-3]))*|(?:\d|0\d|1\d|2[0-3])-(?:\d|0\d|1\d|2[0-3])(?:\/\d+)?) (?:\*(?:\/\d+)?|(?:0?[1-9]|1\d|2\d|3[0-1])(?:,(?:0?[1-9]|1\d|2\d|3[0-1]))*|(?:0?[1-9]|1\d|2\d|3[0-1])-(?:0?[1-9]|1\d|2\d|3[0-1])(?:\/\d+)?) (?:\*(?:\/\d+)?|(?:0?[1-9]|1[0-2])(?:,(?:0?[1-9]|1[0-2]))*|(?:0?[1-9]|1[0-2])-(?:0?[1-9]|1[0-2])(?:\/\d+)?|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC) (?:\*(?:\/\d+)?|0?[0-6](?:,0?[0-6])*|0?[0-6]-0?[0-6](?:\/\d+)?|SUN|MON|TUE|WED|THU|FRI|SAT)$/i', $cronExp)) {
            exit(0);
        } else {
            exit(1);
        }
    }
}
