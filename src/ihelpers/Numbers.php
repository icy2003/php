<?php
/**
 * Class Numbers
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

use icy2003\php\I;

/**
 * 数字处理
 */
class Numbers
{
    /**
     * 比较两个任意精度的数字
     *
     * @param string $number1 数字1
     * @param string $number2 数字2
     * @param integer $scale 精确度，默认精确到小数点后 2 位
     *
     * @return boolean
     */
    public static function isEquals($number1, $number2, $scale = 2)
    {
        return 0 === bccomp($number1, $number2, $scale);
    }

    /**
     * 加载一些奇怪的常量
     *
     * 我知道这些符号不好打，所以从注释里复制喽
     *
     * @constant double π 圆周率
     * @constant double e 自然常数
     * @constant double γ 欧拉常数
     * @constant double φ 黄金比
     * @constant double c 光速
     *
     * @return void
     */
    public static function load()
    {
        I::def('π', M_PI);
        I::def('e', M_E);
        I::def('γ', M_EULER);
        I::def('φ', (sqrt(5) - 1) / 2);
        I::def('c', 2.99792458e8);
    }

    /**
     * 获取数字在指定长度的位置
     *
     * @param integer $number
     * @param integer $length
     *
     * @return integer
     */
    public static function position($number, $length)
    {
        while ($number < 0) {
            $number += $length;
        }
        return $number % $length;
    }

    /**
     * 转成字节数
     *
     * - 支持数字和单位之间有空格
     *
     * @param string $size 例如：10m、10M、10Tb、10kB 等
     *
     * @return integer
     */
    public static function toBytes($size)
    {
        $callback = function ($matches) {
            $sizeMap = [
                '' => 0,
                'b' => 0, // 为了简化正则
                'k' => 1,
                'm' => 2,
                'g' => 3,
                't' => 4,
                'p' => 5,
            ];

            return $matches[1] * pow(1024, $sizeMap[strtolower($matches[2])]);
        };

        return preg_replace_callback('/(\d*)\s*([a-z]?)b?/i', $callback, $size, 1);
    }

    /**
     * 字节数尽可能转成 k、m、g 等形式
     *
     * - 支持小单位转大单位
     *
     * @param integer $bytes
     *
     * @return string
     */
    public static function fromBytes($bytes)
    {
        $bytes = self::toBytes($bytes);
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . ' ' . $unit[$i];
    }

    /**
     * 比较两个值（如：10m、10M、10Tb、10kB 等）的大小
     *
     * - 0：相等，-1：左小于右，1：右小于左
     *
     * @param string $size1
     * @param string $size2
     *
     * @return integer
     */
    public static function compareSize($size1, $size2)
    {
        $bytes1 = self::toBytes($size1);
        $bytes2 = self::toBytes($size2);
        return ($v = $bytes1 - $bytes2) == 0 ? 0 : intval(($v) / abs($v));
    }
}
