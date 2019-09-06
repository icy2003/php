<?php
/**
 * Trait MathAndTrigonometry-L
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

/**
 * MathAndTrigonometry-L
 */
trait LMAT
{
    /**
     * 返回整数的最小公倍数。 最小公倍数是所有整数参数 number1、number2 等的倍数中的最小正整数。 使用 LCM 添加具有不同分母的分数
     *
     * @param integer|array $number1 Number1 是必需的，后续数字是可选的。 要计算其最小公倍数的 1 到 255 个值
     *
     * @return integer
     */
    public static function lcm($number1)
    {
        $numbers = is_array($number1) ? $number1 : func_get_args();
        if (in_array(0, $numbers)) {
            return 0;
        }
        return array_product($numbers) / self::gcd($numbers);
    }

    /**
     * 返回数字的自然对数。 自然对数以常数 e (2.71828182845904) 为底
     *
     * @param double $number 必需。 想要计算其自然对数的正实数
     *
     * @return double
     */
    public static function ln($number)
    {
        return log($number);
    }

    /**
     * 根据指定底数返回数字的对数
     *
     * - 注意：此和 PHP 的 log 的默认底不一样，PHP 的默认底是自然对数 e
     *
     * @param double $number 必需。 想要计算其对数的正实数
     * @param double $base 可选。 对数的底数。 如果省略 base，则假定其值为 10
     *
     * @return double
     */
    public static function log($number, $base = 10)
    {
        return log($number, $base);
    }

    /**
     * 返回数字以 10 为底的对数
     *
     * @param double $number 必需。 想要计算其以 10 为底的对数的正实数
     *
     * @return double
     */
    public static function log10($number)
    {
        return log($number, 10);
    }
}
