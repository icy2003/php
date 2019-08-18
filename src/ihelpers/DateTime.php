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
     * @param integer|null $time 设定该时间戳为今天
     *
     * @return array
     */
    public function dayRange($offset = 0, $time = null)
    {
        null === $time && $time = time();
        $y = (int) (date('Y', $time));
        $m = (int) (date('m', $time));
        $d = (int) (date('d', $time) + $offset);
        return [
            mktime(0, 0, 0, $m, $d, $y),
            mktime(23, 59, 59, $m, $d, $y),
        ];
    }

    /**
     * 距离本周偏移量周数的开始和结束的时间戳
     *
     * - 星期天是第一天，星期六是最后一天！！
     *
     * @param integer $offset
     * @param integer|null $time 设定该时间戳为本周
     *
     * @return array
     */
    public function weekRange($offset = 0, $time = null)
    {
        null === $time && $time = time();
        $offset = (int) $offset;
        return [
            strtotime(date('Y-m-d', strtotime('Sunday ' . ($offset - 1) . ' week', $time))),
            strtotime(date('Y-m-d', strtotime('Saturday ' . $offset . ' week', $time))) + 24 * 3600 - 1,
        ];
    }

    /**
     * 距离本月偏移量月份数的开始和结束的时间戳
     *
     * @param integer $offset
     * @param integer|null $time 设定该时间戳为本月
     *
     * @return array
     */
    public function monthRange($offset = 0, $time = null)
    {
        null === $time && $time = time();
        $y = (int) (date('Y', $time));
        $m = (int) (date('m', $time) + $offset);
        $begin = mktime(0, 0, 0, $m, 1, $y);
        $end = mktime(23, 59, 59, $m, (int) date('t', $begin), $y);

        return [$begin, $end];
    }

    /**
     * 距离今年偏移量年份数的开始和结束的时间戳
     *
     * @param integer $offset
     * @param integer|null $time 设定该时间戳为本年
     *
     * @return array
     */
    public function yearRange($offset = 0, $time = null)
    {
        null === $time && $time = time();
        $year = (int) (date('Y', $time) + $offset);
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
        null === $time && $time = time();
        $offset = (int) $offset;
        return $time + 3600 * 24 * $offset;
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
        null === $time && $time = time();
        $offset = (int) $offset;
        return $time + 3600 * 24 * 7 * $offset;
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
        null === $time && $time = time();
        $offset = (int) $offset;
        return $time + 3600 * 24 * date('t', $time) * $offset;
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
        null === $time && $time = time();
        $offset = (int) $offset;
        return mktime((int) date('H', $time), (int) date('i', $time), (int) date('s', $time), (int) date('m', $time), (int) date('d', $time), (int) date('Y', $time) + 1);
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
        null === $time && $time = time();
        return date('l', $time);
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
        null === $time && $time = time();
        return date('F', $time);
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
        null === $time && $time = time();
        return (bool) date('L', $time);
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
        null === $time && $time = time();
        return (int) date('z', $time) + 1;
    }
}
