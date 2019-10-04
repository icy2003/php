<?php
/**
 * Trait MathAndTrigonometry-A
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;
use icy2003\php\ihelpers\Strings;

/**
 * MathAndTrigonometry-A
 */
trait AMAT
{
    /**
     * 返回数字的绝对值
     *
     * - y = |x|, x ∈ (-∞, +∞)
     *
     * @param double  $number 必需。 需要计算其绝对值的实数
     *
     * @return double
     */
    public static function abs($number)
    {
        return abs($number);
    }

    /**
     * 返回数字的反余弦值
     *
     * - y = arccos(x), x ∈ [-1, 1]
     *
     * @param double $number 必需。 所求角度的余弦值，必须介于 -1 到 1 之间
     *
     * @return double
     */
    public static function acos($number)
    {
        return acos($number);
    }

    /**
     * 返回数字的反双曲余弦值
     *
     * - y = arcosh(x) = ln(x + √(x^2 -1)), x ∈ [1, +∞)
     *
     * @param double $number 必需。 大于或等于 1 的任意实数
     *
     * @return double
     */
    public static function acosh($number)
    {
        return acosh($number);
    }

    /**
     * 返回数字的反余切值的主值
     *
     * - y = arccot(x), x ∈ (-∞, +∞)
     *
     * @param double $number 必需。 Number 为所需角度的余切值。 此值必须是实数
     *
     * @return double
     */
    public static function acot($number)
    {
        return pi() / 2 - atan($number);
    }

    /**
     * 返回数字的反双曲余切值
     *
     * - y = arccoth(x) = arctanh(1 / x), {x| x > 1 或 x < -1}
     *
     * @param double $number 必需。 Number 的绝对值必须大于 1
     *
     * @return double
     */
    public static function acoth($number)
    {
        return atanh(1 / $number);
    }

    /**
     * 将罗马数字转换为阿拉伯数字
     *
     * @param string $string 必需。 用引号引起的字符串、空字符串 ("") 或对包含文本的单元格的引用
     *
     * @return integer
     */
    public static function arabic($string)
    {
        $roman = array(
            'M' => 1000,
            'D' => 500,
            'C' => 100,
            'L' => 50,
            'X' => 10,
            'V' => 5,
            'I' => 1,
        );
        $strlen = Strings::length($string);
        $values = [];
        for ($i = 0; $i < $strlen; $i++) {
            if (isset($roman[strtoupper($string[$i])])) {
                $values[] = $roman[strtoupper($string[$i])];
            }
        }

        $sum = 0;
        while ($current = current($values)) {
            $next = next($values);
            $next > $current ? $sum += $next - $current + 0 * next($values) : $sum += $current;
        }
        return $sum;
    }

    /**
     * 返回数字的反正弦值
     *
     * - y = arcsin(x), x ∈ [-1, 1]
     *
     * @param double $number 必需。 所求角度的正弦值，必须介于 -1 到 1 之间
     *
     * @return double
     */
    public static function asin($number)
    {
        return asin($number);
    }

    /**
     * 返回数字的反双曲正弦值
     *
     * - y = arsinh(x) = ln(x + √(x^2 + 1)), x ∈ (-∞, +∞)
     *
     * @param double $number 必需。 任意实数
     *
     * @return double
     */
    public static function asinh($number)
    {
        return asinh($number);
    }

    /**
     * 返回数字的反正切值
     *
     * - y = arctan(x), x ∈(-π/2, π/2)
     *
     * @param double $number 必需。 所求角度的正切值
     *
     * @return double
     */
    public static function atan($number)
    {
        return atan($number);
    }

    /**
     * 返回给定的 X 轴及 Y 轴坐标值的反正切值
     *
     * @param double $x 必需。 点的 x 坐标
     * @param double $y 必需。 点的 y 坐标
     *
     * @return double
     */
    public static function atan2($x, $y)
    {
        $sign = 1;
        if ($y < 0) {
            $sign = -1;
        }
        if ($x < 0) {
            return $sign * (pi() - self::abs(self::atan($y / $x)));
        } elseif ($x == 0) {
            return pi() / 2 * $sign;
        } else {
            return $sign * self::abs(self::atan($y / $x));
        }
    }

    /**
     * 返回数字的反双曲正切值
     *
     * - y = artanh(x), x ∈(-1, 1) = 1/2 * ln((1 + x) / (1 - x))
     *
     * @param double $number 必需。 -1 到 1 之间的任意实数
     *
     * @return double
     */
    public static function atanh($number)
    {
        return atanh($number);
    }
}
