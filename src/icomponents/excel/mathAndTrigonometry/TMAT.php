<?php
/**
 * Trait MathAndTrigonometry-T
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

/**
 * MathAndTrigonometry-T
 */
trait TMAT
{
    /**
     * 返回已知角度的正切
     *
     * @param double $number 必需。 要求正切的角度，以弧度表示
     *
     * @return double
     */
    public static function tan($number)
    {
        return tan($number);
    }

    /**
     * 返回数字的双曲正切
     *
     * - y = sinh(x) / cosh(x) = (e^x - e^(-x)) / (e^x + e^(-x))
     *
     * @param double $number 必需。 任意实数
     *
     * @return double
     */
    public static function tanh($number)
    {
        return tanh($number);
    }

    /**
     * 将数字的小数部分截去，返回整数
     *
     * @param double $number 必需。 需要截尾取整的数字
     * @param integer $digits 可选。 用于指定取整精度的数字。 num_digits 的默认值为 0（零）
     *
     * @return double
     */
    public static function trunc($number, $digits = 0)
    {
        $sign = self::sign($number);
        return round(abs($number) - 0.5, $digits) * $sign;
    }
}
