<?php
/**
 * Trait MathAndTrigonometry-B
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

/**
 * MathAndTrigonometry-B
 */
trait BMAT
{
    /**
     * 将数字转换为具备给定基数的文本表示
     *
     * @param string $number 必需。 要转换的数字。 必须是大于或等于0且小于 2 ^ 53 的整数
     * @param integer $base 必需。 要将数字转换为的基础基数。 必须是大于或等于2且小于或等于36的整数
     * @param integer $minLength 可选。 返回的字符串的最小长度。 必须是大于或等于0的整数
     *
     * @return string
     */
    public static function base($number, $base, $minLength = null)
    {
        $string = base_convert($number, 10, $base);
        if (null === $minLength) {
            return $string;
        } else {
            return str_pad($string, $minLength, '0', STR_PAD_LEFT);
        }
    }
}
