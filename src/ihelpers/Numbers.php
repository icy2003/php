<?php
/**
 * Class Numbers
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

use icy2003\php\C;
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

        return (int) preg_replace_callback('/(\d*)\s*([a-z]?)b?/i', $callback, $size, 1);
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
        return bccomp($bytes1, $bytes2, 0);
    }

    /**
     * 将某进制的字符串转化成另一进制的字符串
     *
     * @see http://php.net/manual/zh/function.base-convert.php
     *
     * @param string $numberInput 待转换的字符串
     * @param string $fromBaseInput 起始进制的规则
     * @param string $toBaseInput 结束进制的规则
     *
     * @return string
     */
    public static function baseConvert($numberInput, $fromBaseInput, $toBaseInput)
    {
        if ($fromBaseInput == $toBaseInput) {
            return $numberInput;
        }

        $fromBase = Strings::split($fromBaseInput);
        $toBase = Strings::split($toBaseInput);
        $number = Strings::split($numberInput);
        $fromLen = Strings::length($fromBaseInput);
        $toLen = Strings::length($toBaseInput);
        $numberLen = Strings::length($numberInput);
        $retval = '';
        if ($toBaseInput == '0123456789') {
            $retval = 0;
            for ($i = 1; $i <= $numberLen; $i++) {
                $retval = bcadd($retval, bcmul((string) array_search($number[$i - 1], $fromBase), bcpow($fromLen, $numberLen - $i)));
            }

            return $retval;
        }
        if ($fromBaseInput != '0123456789') {
            $base10 = self::baseConvert($numberInput, $fromBaseInput, '0123456789');
        } else {
            $base10 = $numberInput;
        }

        if ($base10 < Strings::length($toBaseInput)) {
            return $toBase[$base10];
        }

        while ($base10 != '0') {
            $retval = $toBase[bcmod($base10, $toLen)] . $retval;
            $base10 = bcdiv($base10, $toLen, 0);
        }
        return $retval;
    }

    /**
     * 进制转换
     *
     * - 支持 2、8、10、16 进制之间转换
     *
     * @param string $number
     * @param integer $fromBase 2、8、10、16之间互转
     * @param integer $toBase 2、8、10、16之间互转
     *
     * @return string
     */
    public static function base($number, $fromBase, $toBase)
    {
        C::assertTrue(in_array($fromBase, [2, 8, 10, 16]), '原始进制必须为 2、8、10、16 之一');
        C::assertTrue(in_array($toBase, [2, 8, 10, 16]), '目标进制必须为 2、8、10、16 之一');
        $chars = '0123456789ABCDEF';
        return self::baseConvert($number, Strings::sub($chars, 0, $fromBase), Strings::sub($chars, 0, $toBase));
    }

    /**
     * 中文大写数字转成小写数字
     *
     * @param string $number
     *
     * @return string
     */
    public static function toLowerChineseCase($number)
    {
        return Strings::replace($number, [
            '零' => '〇',
            '壹' => '一',
            '贰' => '二',
            '叁' => '三',
            '肆' => '四',
            '伍' => '五',
            '陆' => '六',
            '柒' => '七',
            '捌' => '八',
            '玖' => '九',
            '拾' => '十',
            '佰' => '百',
            '仟' => '千',
            '圆' => '元',
        ]);
    }

    /**
     * 中文小写数字转成大写数字
     *
     * @param string $number
     *
     * @return string
     */
    public static function toUpperChineseCase($number)
    {
        return Strings::replace($number, [
            '〇' => '零',
            '一' => '壹',
            '二' => '贰',
            '三' => '叁',
            '四' => '肆',
            '五' => '伍',
            '六' => '陆',
            '七' => '柒',
            '八' => '捌',
            '九' => '玖',
            '十' => '拾',
            '百' => '佰',
            '千' => '仟',
            '元' => '圆',
        ]);
    }
}
