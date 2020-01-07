<?php
/**
 * Trait MathAndTrigonometry-Q
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

/**
 * MathAndTrigonometry-Q
 */
trait QMAT
{
    /**
     * 返回除法的整数部分。 要放弃除法的余数时，可使用此函数
     *
     * @param double $numberator 必需。 被除数
     * @param double $denominator 必需。 除数
     *
     * @return integer
     */
    public static function quotient($numberator, $denominator)
    {
        return (int)($numberator / $denominator);
    }
}
