<?php
/**
 * Trait MathAndTrigonometry-P
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

/**
 * MathAndTrigonometry-P
 */
trait PMAT
{
    /**
     * 数学常量 pi
     *
     * @return double
     */
    public static function pi()
    {
        return pi();
    }

    /**
     * 返回数字乘幂的结果
     *
     * @param double $number  必需。 基数。 可为任意实数
     * @param double $power 必需。 基数乘幂运算的指数
     *
     * @return double
     */
    public static function power($number, $power)
    {
        return pow($number, $power);
    }

    /**
     * PRODUCT函数将以参数形式给出的所有数字相乘
     *
     * @param double $number1 必需。 要相乘的第一个数字或范围
     *
     * @return double
     */
    public static function product($number1)
    {
        $numbers = is_array($number1) ? $number1 : func_get_args();
        return array_product($numbers);
    }
}
