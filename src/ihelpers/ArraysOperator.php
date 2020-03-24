<?php
/**
 * Class ArraysOperator
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2020, icy2003
 */

namespace icy2003\php\ihelpers;

use icy2003\php\I;

/**
 * 数组操作类
 *
 * 常见数组格式的拼装和处理
 */
class ArraysOperator
{

    /**
     * 多个数组
     *
     * @var array
     */
    protected $_array = [];

    /**
     * 筛选出来的多个数组
     *
     * @var array
     */
    protected $_rows = [];

    /**
     * 初始化
     *
     * @param array $array 可传入多个数组
     */
    public function __construct($array)
    {
        $this->_array = func_get_args();
    }

    /**
     * 遍历数组，回调值为 true 时统计次数，到达指定次数时退出遍历
     *
     * @param callback|true $callback 回调函数，true 时遍历整个数组
     * @param integer $times
     *
     * @return static
     */
    public function foreachTimes($callback, $times)
    {
        $array = array_shift($this->_array);
        $rows = [];
        $counter = 0;
        foreach ($array as $key => $value) {
            $result = I::call($callback, [$value, $key]);
            if (true === $result) {
                $counter++;
                $rows[$key] = $value;
            }
            if ($counter === $times) {
                break;
            }
        }
        $this->_rows[] = $rows;

        return $this;
    }

    /**
     * 回调值为 true 时停止遍历
     *
     * @param callback|true $callback 回调函数，true 时遍历整个数组
     *
     * @return static
     */
    public function foreachOnce($callback)
    {
        return $this->foreachTimes($callback, 1);
    }

    /**
     * 遍历数组，回调值为 true 并且再次遇到非 ture 值时统计次数，到达指定次数时退出遍历
     *
     * @param callback|treu $callback 回调函数，true 时遍历整个数组
     * @param integer $times
     *
     * @return static
     */
    public function foreachTimesUntil($callback, $times)
    {
        $array = array_shift($this->_array);
        $rows = [];
        $counter = 0;
        $active = false;
        foreach ($array as $key => $value) {
            $result = I::call($callback, [$value, $key]);
            if (true === $result) {
                if (false === $active) {
                    $counter++;
                    $active = true;
                }
                $rows[$key] = $value;
            } else {
                $active = false;
                if ($counter === $times) {
                    break;
                }
            }
        }
        $this->_rows[] = $rows;

        return $this;
    }

    /**
     * 遍历数组，回调值为 true 并且再次遇到非 ture 值时退出遍历
     *
     * @param callback|true $callback 回调函数，true 时遍历整个数组
     *
     * @return static
     */
    public function foreachOnceUntil($callback)
    {
        return $this->foreachTimesUntil($callback, 1);
    }

    /**
     * 返回被筛选出来的数组
     *
     * - 保留原键
     *
     * @return array
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * 清空被筛选的数组
     *
     * @return void
     */
    public function clear()
    {
        $this->_rows = [];
    }
}
