<?php
/**
 * Class Strings
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

use icy2003\php\I;

/**
 * 字符串类
 *
 * @test icy2003\php_tests\ihelpers\StringsTest
 */
class Strings
{

    use StringsTrait;

    /**
     * 返回字符串的字节长（一个中文等于 3 字节哦~）
     *
     * @param string $string
     *
     * @return integer
     *
     * @tested
     */
    public static function byteLength($string)
    {
        return mb_strlen($string, '8bit');
    }
    /**
     * 返回字符个数（一个中文就是 1 个啦~）
     *
     * @param string $string
     *
     * @return integer
     *
     * @tested
     */
    public static function length($string)
    {
        return mb_strlen($string, 'UTF-8');
    }

    /**
     * 随机数种子（数字）
     */
    const STRINGS_RANDOM_NUMBER = '0123456789';

    /**
     * 随机数种子（小写字母）
     */
    const STRINGS_RANDOM_LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';

    /**
     * 随机数种子（大写字母）
     */
    const STRINGS_RANDOM_UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * 生成随机字符串
     *
     * @param integer $length 随机字符串的长度，默认 32
     * @param string $chars 字符列表，默认为0-9和大小写字母
     *
     * @return string
     *
     * @test
     */
    public static function random($length = 32, $chars = self::STRINGS_RANDOM_NUMBER . self::STRINGS_RANDOM_LOWERCASE . self::STRINGS_RANDOM_UPPERCASE)
    {
        $str = '';
        for ($i = 0; $i < $length; ++$i) {
            $str .= mb_substr($chars, mt_rand(0, self::length($chars) - 1), 1);
        }

        return $str;
    }

    /**
     * 小驼峰转化成下划线（如需要大写下划线，用 strtoupper 转化即可）
     *
     * @param string $string
     *
     * @return string
     *
     * @tested
     */
    public static function toUnderline($string)
    {
        return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $string));
    }

    /**
     * 下划线转化为小驼峰（如需要大驼峰，用 ucfirst 转化即可）
     *
     * @param string $string
     *
     * @return string
     *
     * @tested
     */
    public static function toCamel($string)
    {
        return lcfirst(preg_replace_callback('/_+([a-z0-9_\x7f-\xff])/', function ($matches) {
            return ucfirst($matches[1]);
        }, strtolower($string)));
    }

    /**
     * 格式化成标题格式（每个单词首字母大写）
     *
     * @param string $string
     *
     * @return string
     *
     * @tested
     */
    public static function toTitle($string)
    {
        return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * 检查字符串是否以某字符串开头
     *
     * @param string $string
     * @param string $search 待搜索子字符串
     *
     * @return boolean
     *
     * @tested
     */
    public static function isStartsWith($string, $search)
    {
        return (string) $search !== "" && mb_strpos($string, $search) === 0;
    }

    /**
     * 检查字符串是否以某字符串结尾
     *
     * @param string $string
     * @param string $search 待搜索子字符串
     *
     * @return boolean
     *
     * @tested
     */
    public static function isEndsWith($string, $search)
    {
        return (string) $search !== "" && mb_substr($string, -static::length($search)) === $search;
    }

    /**
     * 检查字符串中是否包含某字符串
     *
     * @param string $string
     * @param string $search 待搜索子字符串
     * @param integer $pos 如果找到子串，则引用出子串的起始位置
     *
     * @return boolean
     *
     * @tested
     */
    public static function isContains($string, $search, &$pos = null)
    {
        return (string) $search !== "" && ($pos = mb_strpos($string, $search)) !== false;
    }

    /**
     * 在字符串里找子串的前部分
     *
     * @param string $string
     * @param string $search
     * @param integer $pos 如果找到子串，则引用出子串的起始位置
     *
     * @return string
     *
     * @tested
     */
    public static function partBefore($string, $search, &$pos = null)
    {
        if (self::isContains($string, $search, $pos)) {
            return mb_substr($string, 0, $pos);
        }
        return "";
    }

    /**
     * 在字符串里找子串的后部分
     *
     * @param string $string
     * @param string $search
     * @param integer $pos 如果找到子串，则引用出子串的起始位置
     *
     * @return string
     *
     * @tested
     */
    public static function partAfter($string, $search, &$pos = null)
    {
        if (self::isContains($string, $search, $pos)) {
            return mb_substr($string, $pos + self::length($search), self::length($string) - 1);
        }
        return "";
    }

    /**
     * 反转字符串，支持中文
     *
     * @param string $string
     *
     * @return string
     *
     * @tested
     */
    public static function reverse($string)
    {
        return implode('', array_reverse(self::split($string)));
    }

    /**
     * 把字符串打散为数组
     *
     * @param string $string
     *
     * @return array
     *
     * @tested
     */
    public static function split($string)
    {
        return preg_split('/(?<!^)(?!$)/u', $string);
    }

    /**
     * 拆分成数组
     *
     * @param array|string $mixed 数组或者字符串
     * @param string $delimiter 分隔符，默认英文逗号（,）
     * @param boolean $combine 是否合并相同元素，默认 false，即不合并
     *
     * @return array
     *
     * @tested
     */
    public static function toArray($mixed, $delimiter = ',', $combine = false)
    {
        if (is_array($mixed)) {
            $mixed = implode($delimiter, $mixed);
        }
        $array = explode($delimiter, $mixed);
        if (true === $combine) {
            $array = Arrays::toPart($array);
        }
        return $array;
    }

    /**
     * 返回字符串的子串
     *
     * @param string $string
     * @param integer $start 起始位置
     * @param integer|null $length 子串长度，默认为 null，即返回剩下的部分
     *
     * @return string
     *
     * @tested
     */
    public static function sub($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length);
    }

    /**
     * 字符串转数字
     *
     * - 正则为 `/^\d\.*\d*[e|E]/` 的字符串会……，这是 PHP 特性！如果你不喜欢 PHP，右上角
     *
     * @param string $string
     *
     * @return double|integer
     *
     * @tested
     */
    public static function toNumber($string)
    {
        return $string + 0;
    }

    /**
     * 用回调将分隔符拆分出来的字符串执行后，用分隔符合并回去
     *
     * @param callback $callback 回调
     * @param string $string
     * @param string $delimiter 分隔符，默认英文逗号（,）
     *
     * @return string
     */
    public static function map($callback, $string, $delimiter = ',')
    {
        $arr = [];
        $parts = explode($delimiter, $string);
        foreach ($parts as $part) {
            $arr[] = I::trigger($callback, [$part]);
        }
        return implode($delimiter, $arr);
    }

    /**
     * 重复一个字符若干次
     *
     * @param string $char
     * @param integer $num
     * @param integer $maxLength 最大重复次数，默认不限制
     *
     * @return string
     */
    public static function repeat($char, $num, $maxLength = null)
    {
        $length = null === $maxLength ? $num : min($maxLength, $num);
        return str_repeat($char, $length);
    }
}
