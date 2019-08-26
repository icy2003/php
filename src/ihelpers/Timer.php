<?php
/**
 * Class Timer
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

/**
 * 计时器
 */
class Timer
{
    /**
     * 计数器
     *
     * @var array
     */
    protected static $_timers = [];

    /**
     * 计时开始
     *
     * @param string $name 开始名
     *
     * @return string
     */
    public static function start($name = '')
    {
        $start = microtime(true);
        if (empty($name)) {
            $name = md5($start . rand(0, 100));
        }
        static::$_timers[$name] = $start;

        return $name;
    }

    /**
     * 计时结束
     *
     * @param string $name 结束名
     *
     * @return float
     */
    public static function end($name)
    {
        $delta = 0.0;
        if (isset(static::$_timers[$name])) {
            $delta = microtime(true) - static::$_timers[$name];
            unset(static::$_timers[$name]);
        }

        return $delta;
    }

    /**
     * 计时器
     *
     * @return float
     */
    public static function timer()
    {
        static $time = 0;
        $previousTime = $time;
        $time = microtime(true);

        return (0 === $previousTime) ? 0.0 : ($time - $previousTime);
    }
}
