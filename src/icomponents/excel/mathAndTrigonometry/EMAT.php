<?php
/**
 * Trait MathAndTrigonometry-E
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

/**
 * MathAndTrigonometry-E
 */
trait EMAT
{
    /**
     * 返回数字向上舍入到的最接近的偶数
     *
     * - 数值总是向绝对值大的方向舍入
     *
     * @param double $number 必需。 要舍入的值
     *
     * @return integer
     */
    public static function even($number)
    {
        $sign = self::sign($number);
        $number = ceil(self::abs($number));
        if ($number % 2 == 1) {
            $number += 1;
        }
        return (int) ($sign * $number);
    }

    /**
     * 返回 e 的 n 次幂。 常数 e 等于 2.71828182845904，是自然对数的底数
     *
     * @param integer $number
     *
     * @return double
     */
    public static function exp($number)
    {
        return exp($number);
    }
}
