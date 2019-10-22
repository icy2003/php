<?php
/**
 * Trait MathAndTrigonometry-R
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

/**
 * MathAndTrigonometry-R
 */
trait RMAT
{
    /**
     * 将度数转换为弧度
     *
     * @param double $angle 必需。 要转换的以度数表示的角度
     *
     * @return double
     */
    public static function radians($angle)
    {
        return deg2rad($angle);
    }

    /**
     * 返回了一个大于等于 0 且小于 1 的平均分布的随机实数
     *
     * @return double
     */
    public static function rand()
    {
        return mt_rand(0, (int) self::power(10, 10)) / self::power(10, 10);
    }

    /**
     * 函数返回一组随机数字。 可指定要填充的行数和列数，最小值和最大值，以及是否返回整数或小数值
     *
     * @param integer $row 要返回的行数
     * @param integer $col 要返回的列数
     * @param integer $min 想返回的最小数值
     * @param integer $max 想返回的最大数值
     * @param boolean $isInt 返回整数或小数
     *
     * @return array
     */
    public static function randarray($row = 1, $col = 1, $min = 0, $max = null, $isInt = false)
    {
        null === $max && $max = (int) pow(10, 10);
        $array = [];
        for ($i = 0; $i < $row; $i++) {
            for ($j = 0; $j < $col; $j++) {
                $array[$i][$j] = mt_rand($min, $max - 1) + ($isInt ? 0 : mt_rand(0, (int) pow(10, 10)) / pow(10, 10));
            }
        }
        return $array;
    }

    /**
     * 返回位于两个指定数之间的一个随机整数
     *
     * @param integer $bottom 必需。 RANDBETWEEN 将返回的最小整数
     * @param integer $top 必需。 RANDBETWEEN 将返回的最大整数
     *
     * @return integer
     */
    public static function randbetween($bottom, $top)
    {
        return mt_rand($bottom, $top);
    }

    /**
     * 将阿拉伯数字转换为文字形式的罗马数字
     *
     * - 注意：不支持简明版
     *
     * @param integer $number
     *
     * @return string|false
     */
    public static function roman($number)
    {
        if (!is_numeric($number) || $number > 3999 || $number <= 0) {
            return false;
        }

        $roman = array(
            'M' => 1000,
            'D' => 500,
            'C' => 100,
            'L' => 50,
            'X' => 10,
            'V' => 5,
            'I' => 1,
        );
        $amount = [];
        foreach ($roman as $k => $v) {
            if (($amount[$k] = floor($number / $v)) > 0) {
                $number -= $amount[$k] * $v;
            }
        }

        // Build the string:
        $return = '';
        $oldK = '';
        foreach ($amount as $k => $v) {
            $return .= $v <= 3 ? str_repeat($k, $v) : $k . $oldK;
            $oldK = $k;
        }
        return str_replace(array('VIV', 'LXL', 'DCD'), array('IX', 'XC', 'CM'), $return);
    }

    /**
     * 函数将数字四舍五入到指定的位数
     *
     * @param double $number 必需。 要四舍五入的数字
     * @param integer $digits 要进行四舍五入运算的位数
     *
     * @return double
     */
    public static function round($number, $digits = 0)
    {
        return round($number, $digits);
    }

    /**
     * 朝着零的方向将数字进行向下舍入
     *
     * @param double $number 必需。需要向下舍入的任意实数
     * @param integer $digits 要将数字舍入到的位数
     *
     * @return double
     */
    public static function rounddown($number, $digits = 0)
    {
        $sign = $number >= 0 ? 1 : -1;
        return round(abs($number) - 0.5 * pow(10, -$digits), $digits) * $sign;
    }

    /**
     * 朝着远离 0（零）的方向将数字进行向上舍入
     *
     * @param double $number 必需。需要向下舍入的任意实数
     * @param integer $digits 要将数字舍入到的位数
     *
     * @return double
     */
    public static function roundup($number, $digits = 0)
    {
        $sign = $number >= 0 ? 1 : -1;
        return round(abs($number) + 0.5 * pow(10, -$digits), $digits) * $sign;
    }
}
