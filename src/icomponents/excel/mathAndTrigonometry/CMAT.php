<?php
/**
 * Trait MathAndTrigonometry-C
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

/**
 * MathAndTrigonometry-C
 */
trait CMAT
{

    /**
     * 返回将参数 number 向上舍入（沿绝对值增大的方向）为最接近的指定基数的倍数
     *
     * @param double $number  必需。 要舍入的值
     * @param double $significance 必需。 要舍入到的倍数
     *
     * @return double
     */
    public static function ceiling($number, $significance = 1)
    {
        return is_numeric($number) ? (ceil($number / $significance) * $significance) : false;
    }

    /**
     * 返回给定数目的项目的组合数
     *
     * - C(m,n) = n! / (m! * (n - m)!)，其中 0 ≤ m ≤ n
     *
     * @param integer $n 必需。 项目的数量
     * @param integer $m 必需。 每一组合中项目的数量
     *
     * @return integer
     */
    public static function combin($n, $m)
    {
        return (self::fact($n) / (self::fact($m) * self::fact($n - $m)));
    }

    /**
     * 返回给定数目的项的组合数（包含重复）
     *
     * @param integer $n 必需。 必须大于或等于 0 并大于或等于 m 非整数值将被截尾取整
     * @param integer $m 必需。 必须大于或等于 0。 非整数值将被截尾取整
     *
     * @return integer
     */
    public static function combina($n, $m)
    {
        return self::combin($n + $m - 1, $n - 1);
    }

    /**
     * 返回已知角度的余弦值
     *
     * @param double $number 必需。 想要求余弦的角度，以弧度表示
     *
     * @return double
     */
    public static function cos($number)
    {
        return cos($number);
    }

    /**
     * 返回数字的双曲余弦值
     *
     * - y = cosh(x) = (e^x + e^(-x)) / 2
     *
     * @param double $number 必需。 想要求双曲余弦的任意实数
     *
     * @return double
     */
    public static function cosh($number)
    {
        return cosh($number);
    }

    /**
     * 返回以弧度表示的角度的余切值
     *
     * @param double $number 必需。 要获得其余切值的角度，以弧度表示
     *
     * @return double
     */
    public static function cot($number)
    {
        return -tan(pi() / 2 + $number);
    }

    /**
     * 返回一个双曲角度的双曲余切值
     *
     * - y = coth(x) = 1 / tanh(x) = (e^x + e^(-x)) / (e^x - e^(-x))
     *
     * @param double $number 必需
     *
     * @return double
     */
    public static function coth($number)
    {
        return 1 / tanh($number);
    }

    /**
     * 返回角度的余割值，以弧度表示
     *
     * @param double $number 必需
     *
     * @return double
     */
    public static function csc($number)
    {
        return 1 / sin($number);
    }

    /**
     * 返回角度的双曲余割值，以弧度表示
     *
     * - y = csch(x) = 1 / sinh(x) = 2 / (e^x - e^(-x))
     *
     * @param double $number 必需
     *
     * @return double
     */
    public static function csch($number)
    {
        return 1 / sinh($number);
    }
}
