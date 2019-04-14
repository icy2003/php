<?php
/**
 * Class Magic
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

/**
 * 一个奇怪的类
 */
class Magic
{
    /**
     * 消除尾递归.
     *
     * @param callable $callback 使用匿名函数尾调用的函数
     * @param array    $params   函数参数
     *
     * @example
     *
     * 以斐波那契函数为例子
     * function factorial($n, $accumulator = 1)
     * {
     *     if (0 == $n) {
     *        return $accumulator;
     *     }
     *     return function () use ($n, $accumulator) {
     *        return factorial($n - 1, $accumulator * $n);
     *     };
     * }
     * Magic::tailRecursion('factorial', array(100));
     *
     * @return mixed
     */
    public static function tailRecursion($callback, $params)
    {
        $result = call_user_func_array($callback, $params);
        while (is_callable($result)) {
            $result = $result();
        }

        return $result;
    }
}
