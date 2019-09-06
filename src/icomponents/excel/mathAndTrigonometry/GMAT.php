<?php
/**
 * Trait MathAndTrigonometry-G
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

/**
 * MathAndTrigonometry-G
 */
trait GMAT
{
    /**
     * 返回两个或多个整数的最大公约数。 最大公约数是能够同时整除 number1 和 number2 而没有余数的最大整数
     *
     * @param integer|array $number1 Number1 是必需的，后续数字是可选的。 介于 1 和 255 之间的值
     *
     * @return integer
     */
    public static function gcd($number1)
    {
        $numbers = is_array($number1) ? $number1 : func_get_args();
        $n = min($numbers);
        if (in_array(0, $numbers)) {
            return max($numbers);
        }
        for ($i = $n; $i > 1; $i--) {
            $isFind = true;
            foreach ($numbers as $num) {
                if (false === is_int($num / $i)) {
                    $isFind = false;
                    break;
                }
            }
            if (true === $isFind) {
                return $i;
            }
        }
        return 1;
    }
}
