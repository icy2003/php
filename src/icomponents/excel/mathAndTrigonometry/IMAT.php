<?php
/**
 * Trait MathAndTrigonometry-I
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

/**
 * MathAndTrigonometry-I
 */
trait IMAT
{
    /**
     * 将数字向下舍入到最接近的整数
     *
     * - int 是关键字，这里用上后缀 _i
     *
     * @param double $number 必需。 需要进行向下舍入取整的实数
     *
     * @return integer
     */
    public static function int_i($number)
    {
        return floor($number);
    }
}
