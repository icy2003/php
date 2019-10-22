<?php
/**
 * Trait MathAndTrigonometry-F
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

use icy2003\php\ihelpers\Arrays;

/**
 * MathAndTrigonometry-F
 */
trait FMAT
{
    /**
     * 返回数的阶乘
     *
     * @param integer|double $number 必需。 要计算其阶乘的非负数。 如果 number 不是整数，将被截尾取整
     *
     * @return integer|false
     */
    public static function fact($number)
    {
        $number = (int) floor($number);
        if ($number == 0) {
            return 1;
        } elseif ($number < 0) {
            return false;
        }
        $result = 1;
        foreach (Arrays::rangeGenerator(1, $number) as $num) {
            $result *= $num;
        }
        return $result;
    }

    /**
     * 返回数字的双倍阶乘
     *
     * - 偶数：n!! = n * (n - 2) * (n - 4) * ... * 4 * 2
     * - 奇数：n!! = n * (n - 2) * (n - 4) * ... * 3 * 1
     *
     * @param integer $number 必需。 为其返回双倍阶乘的值。 如果 number 不是整数，将被截尾取整
     *
     * @return integer
     */
    public static function factdouble($number)
    {
        if ($number == 0) {
            return 1;
        }
        $result = 1;
        $isEven = $number % 2 == 0;
        foreach (Arrays::rangeGenerator($isEven ? 2 : 1, (int) $number, 2) as $num) {
            $result *= $num;
        }
        return $result;
    }

    /**
     * 将参数 number 向下舍入（沿绝对值减小的方向）为最接近的 significance 的倍数
     *
     * @param double $number 必需。 要舍入的数值
     * @param double $significance 必需。 要舍入到的倍数
     *
     * @return double|false
     */
    public static function floor($number, $significance = 1)
    {
        return (is_numeric($number) && is_numeric($significance)) && ($number * $significance > 0) ? (floor($number / $significance) * $significance) : false;
    }
}
