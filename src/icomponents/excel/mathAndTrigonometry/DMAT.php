<?php
/**
 * Trait MathAndTrigonometry-D
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

/**
 * MathAndTrigonometry-D
 */
trait DMAT
{
    /**
     * 按给定基数将数字的文本表示形式转换成十进制数
     *
     * @param string $text 必需
     * @param integer $radix 必需。Radix 必须是整数
     *
     * @return string
     */
    public static function decimal($text, $radix)
    {
        return base_convert($text, $radix, 10);
    }

    /**
     * 将弧度转换为度
     *
     * @param double $angle
     *
     * @return double
     */
    public static function degrees($angle)
    {
        return rad2deg($angle);
    }
}
