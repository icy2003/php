<?php

namespace icy2003\php\ihelpers;

/**
 * 计时器
 *
 * @filename Timer.php
 * @encoding UTF-8
 *
 * @author icy2003 <2317216477@qq.com>
 */
class Timer
{
    private static $__timers = [];

    public static function start($name = '')
    {
        $start = microtime(true);
        if (empty($name)) {
            $name = md5($start . rand(0, 100));
        }
        self::$timers[$name] = $start;

        return $name;
    }

    public static function end($name)
    {
        $delta = 0.0;
        if (isset(self::$timers[$name])) {
            $delta = microtime(true) - self::$timers[$name];
            unset(self::$timers[$name]);
        }

        return $delta;
    }

    public static function timer()
    {
        static $time = 0;
        $previousTime = $time;
        $time = microtime(true);

        return (0 === $previousTime) ? 0.0 : ($time - $previousTime);
    }
}
