<?php
/**
 * Class DateTime
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

use icy2003\php\I;

/**
 * 日期类
 */
class DateTime
{

    /**
     * 构造函数
     *
     * @param string $timezone 时区设置，默认上海
     */
    public function __construct($timezone = 'Asia/Shanghai')
    {
        date_default_timezone_set($timezone);
    }

    /**
     * 距离今天偏移量天数的开始和结束时间戳
     *
     * - 0：今天，1：明天，-1：昨天，以此类推
     *
     * @param integer $offset 天数偏移量，默认 0，即今天
     *
     * @return array
     */
    public function rangeDay($offset = 0)
    {
        $day = (int) (date('d') + $offset);
        return [
            mktime(0, 0, 0, (int) date('m'), $day, (int) date('Y')),
            mktime(23, 59, 59, (int) date('m'), $day, (int) date('Y')),
        ];
    }

    /**
     * 距离本周偏移量周数的开始和结束的时间戳
     *
     * - 星期天是第一天，星期六是最后一天！！
     *
     * @param integer $offset
     *
     * @return array
     */
    public function ramgeWeek($offset = 0)
    {
        $timestamp = time();
        $offset = (int) $offset;
        return [
            strtotime(date('Y-m-d', strtotime('Sunday ' . ($offset - 1) . ' week', $timestamp))),
            strtotime(date('Y-m-d', strtotime('Saturday ' . $offset . ' week', $timestamp))) + 24 * 3600 - 1,
        ];
    }

    /**
     * 距离本月偏移量月份数的开始和结束的时间戳
     *
     * @param integer $offset
     *
     * @return array
     */
    public function rangeMonth($offset = 0)
    {
        $month = (int) (date('m') + $offset);
        $begin = mktime(0, 0, 0, $month, 1, (int) date('Y'));
        $end = mktime(23, 59, 59, $month, (int) date('t', $begin), (int) date('Y'));

        return [$begin, $end];
    }

    /**
     * 距离今年偏移量年份数的开始和结束的时间戳
     *
     * @param integer $offset
     *
     * @return array
     */
    public function rangeYear($offset = 0)
    {
        $year = (int) (date('Y') + $offset);
        return [
            mktime(0, 0, 0, 1, 1, $year),
            mktime(23, 59, 59, 12, 31, $year),
        ];
    }

    /**
     * 距离某时间偏移量天数的时间戳
     *
     * @param integer $offset
     * @param integer|null $time 不给则取当前时间
     *
     * @return integer
     */
    public function day($offset = 0, $time = null)
    {
        $timestamp = null === $time ? time() : $time;
        $offset = (int) $offset;
        return $timestamp + 3600 * 24 * $offset;
    }

    /**
     * 距离某时间偏移量星期数的时间戳
     *
     * @param integer $offset
     * @param integer|null $time 不给则取当前时间
     *
     * @return integer
     */
    public function week($offset = 0, $time = null)
    {
        $timestamp = null === $time ? time() : $time;
        $offset = (int) $offset;
        return $timestamp + 3600 * 24 * 7 * $offset;
    }

    /**
     * 距离某时间偏移量月份数的时间戳
     *
     * @param integer $offset
     * @param integer|null $time 不给则取当前时间
     *
     * @return integer
     */
    public function month($offset = 0, $time = null)
    {
        $timestamp = null === $time ? time() : $time;
        $offset = (int) $offset;
        return $timestamp + 3600 * 24 * date('t', $timestamp) * $offset;
    }

    /**
     * 距离某时间偏移量年份数的时间戳
     *
     * @param integer $offset
     * @param integer|null $time 不给则取当前时间
     *
     * @return integer
     */
    public function year($offset = 0, $time = null)
    {
        $timestamp = null === $time ? time() : $time;
        $offset = (int) $offset;
        return $timestamp + 3600 * 24 * (date('z', mktime(0, 0, 0, 12, 31, date('Y', $timestamp))) + 1) * $offset;
    }

    /**
     * 返回星期几的英文名
     *
     * @param integer|null $time
     *
     * @return string
     */
    public function weekName($time = null)
    {
        $timestamp = null === $time ? time() : $time;
        return date('l', $timestamp);
    }

    /**
     * 返回月份名
     *
     * @param integer|null $time
     *
     * @return string
     */
    public function monthName($time = null)
    {
        $timestamp = null === $time ? time() : $time;
        return date('F', $timestamp);
    }

    /**
     * 是否是闰年
     *
     * @param integer|null $time
     *
     * @return boolean
     */
    public function isLeapYear($time = null)
    {
        $timestamp = null === $time ? time() : $time;
        return (bool) date('L', $timestamp);
    }

    /**
     * 返回该年的第几天
     *
     * - 1 月 1 日为第一天
     *
     * @param integer|null $time
     *
     * @return integer
     */
    public function yearPosition($time = null)
    {
        $timestamp = null === $time ? time() : $time;
        return (int) date('z', $timestamp) + 1;
    }
}
