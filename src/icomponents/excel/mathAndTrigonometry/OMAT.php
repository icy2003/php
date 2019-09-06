<?php
/**
 * Trait MathAndTrigonometry-O
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

/**
 * MathAndTrigonometry-O
 */
trait OMAT
{
    /**
     * 返回数字向上舍入到的最接近的奇数
     *
     * @param double $number 必需。 要舍入的值
     *
     * @return integer
     */
    public static function odd($number)
    {
        $number = ceil($number);
        if ($number % 2 == 0) {
            $number += 1;
        }
        return (int) $number;
    }
}
