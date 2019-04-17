<?php
/**
 * Class DateTime
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

/**
 * 日期类
 */
class DateTime
{

    /**
     * 返回今天开始和结束的时间戳
     *
     * @return array
     */
    public static function today()
    {
        return [
            mktime(0, 0, 0, date('m'), date('d'), date('Y')),
            mktime(23, 59, 59, date('m'), date('d'), date('Y')),
        ];
    }

    /**
     * 返回昨天开始和结束的时间戳
     *
     * @return array
     */
    public static function yesterday()
    {
        $yesterday = date('d') - 1;
        return [
            mktime(0, 0, 0, date('m'), $yesterday, date('Y')),
            mktime(23, 59, 59, date('m'), $yesterday, date('Y')),
        ];
    }

    /**
     * 返回明天开始和结束的时间戳
     *
     * @return array
     */
    public static function tomorrow()
    {
        $tomorrow = date('d') + 1;
        return [
            mktime(0, 0, 0, date('m'), $tomorrow, date('Y')),
            mktime(23, 59, 59, date('m'), $tomorrow, date('Y')),
        ];
    }

    /**
     * 返回本周开始和结束的时间戳
     *
     * @return array
     */
    public static function week()
    {
        $timestamp = time();
        return [
            strtotime(date('Y-m-d', strtotime('+0 week Monday', $timestamp))),
            strtotime(date('Y-m-d', strtotime('+0 week Sunday', $timestamp))) + 24 * 3600 - 1,
        ];
    }

    /**
     * 返回上周开始和结束的时间戳
     *
     * @return array
     */
    public static function lastWeek()
    {
        $timestamp = time();
        return [
            strtotime(date('Y-m-d', strtotime('last week Monday', $timestamp))),
            strtotime(date('Y-m-d', strtotime('last week Sunday', $timestamp))) + 24 * 3600 - 1,
        ];
    }

    /**
     * 返回下周开始和结束的时间戳
     *
     * @return array
     */
    public static function nextWeek()
    {
        $timestamp = time();
        return [
            strtotime(date('Y-m-d', strtotime('next week Monday', $timestamp))),
            strtotime(date('Y-m-d', strtotime('next week Sunday', $timestamp))) + 24 * 3600 - 1,
        ];
    }

    /**
     * 返回本月开始和结束的时间戳
     *
     * @return array
     */
    public static function month()
    {
        return [
            mktime(0, 0, 0, date('m'), 1, date('Y')),
            mktime(23, 59, 59, date('m'), date('t'), date('Y')),
        ];
    }

    /**
     * 返回上个月开始和结束的时间戳
     *
     * @return array
     */
    public static function lastMonth()
    {
        $begin = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
        $end = mktime(23, 59, 59, date('m') - 1, date('t', $begin), date('Y'));

        return [$begin, $end];
    }

    /**
     * 返回下个月开始和结束的时间戳
     *
     * @return array
     */
    public static function nextMonth()
    {
        $begin = mktime(0, 0, 0, date('m') + 1, 1, date('Y'));
        $end = mktime(23, 59, 59, date('m') + 1, date('t', $begin), date('Y'));

        return [$begin, $end];
    }

    /**
     * 返回今年开始和结束的时间戳
     *
     * @return array
     */
    public static function year()
    {
        return [
            mktime(0, 0, 0, 1, 1, date('Y')),
            mktime(23, 59, 59, 12, 31, date('Y')),
        ];
    }

    /**
     * 返回去年开始和结束的时间戳
     *
     * @return array
     */
    public static function lastYear()
    {
        $year = date('Y') - 1;
        return [
            mktime(0, 0, 0, 1, 1, $year),
            mktime(23, 59, 59, 12, 31, $year),
        ];
    }

    /**
     * 返回明年开始和结束的时间戳
     *
     * @return array
     */
    public static function nextYear()
    {
        $year = date('Y') + 1;
        return [
            mktime(0, 0, 0, 1, 1, $year),
            mktime(23, 59, 59, 12, 31, $year),
        ];
    }

    /**
     * 天数转换成秒数
     *
     * @param int $day
     * @return int
     */
    public static function dayToSecond($day = 1)
    {
        return $day * 86400;
    }

    /**
     * 周数转换成秒数
     *
     * @param int $week
     * @return int
     */
    public static function weekToSecond($week = 1)
    {
        return self::daysToSecond() * 7 * $week;
    }

    /**
     * 返回几天前的时间戳
     *
     * @param int $day
     * @return int
     */
    public static function daysAgo($day = 1)
    {
        $nowTime = time();
        return $nowTime - self::daysToSecond($day);
    }

    /**
     * 返回几天后的时间戳
     *
     * @param int $day
     * @return int
     */
    public static function daysAfter($day = 1)
    {
        $nowTime = time();
        return $nowTime + self::daysToSecond($day);
    }
}
