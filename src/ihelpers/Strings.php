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
 */
class Strings
{

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
     * 返回字符串的字节长（一个中文等于 3 字节哦~）
     *
     * @param string $string
     *
     * @return integer
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
     */
    public static function length($string)
    {
        return mb_strlen($string, 'UTF-8');
    }

    /**
     * 生成随机字符串
     *
     * @param integer $length 随机字符串的长度，默认 32
     * @param string $chars 字符列表，默认为0-9和大小写字母
     *
     * @return string
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
     * 生成密码 hash
     *
     * @param string $password 原始密码
     * @param integer $cost
     *
     * @return string
     */
    public static function generatePasswordHash($password, $cost = null)
    {
        null === $cost && $cost = 13;
        // PHP 5 >= 5.5.0, PHP 7
        if (function_exists('password_hash')) {
            return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
        }
        if ($cost < 4 || $cost > 31) {
            throw new \Exception('cost 必须大于等于 4，小于等于 31');
        }
        $salt = sprintf('$2y$%02d$', $cost);
        $salt .= str_replace('+', '.', substr(base64_encode(static::random(20)), 0, 22));
        $hash = crypt($password, $salt);
        if (!is_string($hash) || strlen($hash) !== 60) {
            throw new \Exception('未知错误');
        }
        return $hash;
    }

    /**
     * 验证密码
     *
     * @param string $password 原始密码
     * @param string $hash HASH 后的密码，需配合 Strings::generatePasswordHash
     *
     * @return boolean
     */
    public static function validatePassword($password, $hash)
    {
        if (!is_string($password) || $password === '') {
            return false;
        }
        $matches = [];
        if (!preg_match('/^\$2[axy]\$(\d\d)\$[\.\/0-9A-Za-z]{22}/', $hash, $matches)
            || $matches[1] < 4
            || $matches[1] > 30) {
            return false;
        }
        // PHP 5 >= 5.5.0, PHP 7
        if (function_exists('password_verify')) {
            return password_verify($password, $hash);
        }

        $test = crypt($password, $hash);
        $n = strlen($test);
        if ($n !== 60) {
            return false;
        }
        // PHP 5 >= 5.6.0, PHP 7
        if (function_exists('hash_equals')) {
            return hash_equals($test, $hash);
        }

        $test .= '\0';
        $hash .= '\0';
        $expectedLength = static::byteLength($test);
        $actualLength = static::byteLength($hash);
        $diff = $expectedLength - $actualLength;
        for ($i = 0; $i < $actualLength; $i++) {
            $diff |= (ord($hash[$i]) ^ ord($test[$i % $expectedLength]));
        }

        return $diff === 0;
    }

    /**
     * 小驼峰转化成下划线（如需要大写下划线，用 strtoupper 转化即可）
     *
     * @param string $string
     *
     * @return string
     */
    public static function camel2underline($string)
    {
        return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $string));
    }

    /**
     * 下划线转化为小驼峰（如需要大驼峰，用 ucfirst 转化即可）
     *
     * @param string $string
     *
     * @return string
     */
    public static function underline2camel($string)
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
     */
    public static function formatAsTitle($string)
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
     *
     * @return boolean
     */
    public static function isContains($string, $search)
    {
        return (string) $search !== "" && mb_strpos($string, $search) !== false;
    }

    /**
     * 在字符串里找子串的前部分
     *
     * @param string $string
     * @param string $search
     *
     * @return string
     */
    public static function partBefore($string, $search)
    {
        if (self::isContains($string, $search)) {
            return mb_substr($string, 0, mb_strpos($string, $search));
        }
        return "";
    }

    /**
     * 在字符串里找子串的后部分
     *
     * @param string $string
     * @param string $search
     *
     * @return string
     */
    public static function partAfter($string, $search)
    {
        if (self::isContains($string, $search)) {
            return mb_substr($string, mb_strpos($string, $search) + 1, self::length($string) - 1);
        }
        return "";
    }

    /**
     * 反转字符串，支持中文
     *
     * @param string $string
     *
     * @return string
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
     */
    public static function split($string)
    {
        return preg_split('/(?<!^)(?!$)/u', $string);
    }

    /**
     * 多次换行
     *
     * @param integer $num 换行次数
     *
     * @return string
     */
    public static function eol($num = 1)
    {
        return str_repeat(PHP_EOL, $num);
    }

    /**
     * 返回字符串占用行数
     *
     * @param string $string
     *
     * @return integer
     */
    public static function lineNumber($string)
    {
        $array = explode(PHP_EOL, $string);
        return Arrays::count($array);
    }

    /**
     * 字符串转成变量
     *
     * - 变量是被如：{{}}括起来的字符串
     * - 例如：{{var}}
     *
     * @param string $name
     * @param array $boundary 边界符，默认 ['{{', '}}']
     *
     * @return string
     */
    public static function toVariable($name, $boundary = ['{{', '}}'])
    {
        if (is_string($boundary)) {
            $boundary = [$boundary, $boundary];
        }
        return self::isContains($name, $boundary[0]) ? $name : $boundary[0] . $name . $boundary[1];
    }

    /**
     * 判断一个字符串是否是变量
     *
     * @param string $name
     * @param array $boundary 边界符，默认 ['{{', '}}']
     *
     * @return boolean
     */
    public static function isVariable($name, $boundary = ['{{', '}}'])
    {
        return self::isStartsWith($name, $boundary[0]) && self::isEndsWith($name, $boundary[1]);
    }

    /**
     * 计算包含变量的字符串
     *
     * @param string $text
     * @param array $array 键值对形式：[{{键}} => 值]。如果键不是变量，则不会替换进 $text
     * @param array $boundary 边界符，默认 ['{{', '}}']
     *
     * @return string
     */
    public static function fromVariable($text, $array, $boundary = ['{{', '}}'])
    {
        $data = [];
        foreach ($array as $name => $value) {
            self::isVariable($name, $boundary) && $data[$name] = $value;
        }
        return str_replace(array_keys($data), array_values($data), $text);
    }

    /**
     * 判断两个字符串像不像
     *
     * - 图形验证码里经常有人把 o 看成 0，所以……
     * - 例如：hello 和 hell0 看起来是像的 (-w-)o~
     *
     * @param string $string1 第一个字符串
     * @param string $string2 第二个字符串
     * @param array $array 看起来像的字符的列表，默认 ['0o', 'yv', 'ij', '1l']
     *
     * @return boolean
     */
    public static function looksLike($string1, $string2, $array = ['0oO', 'yv', 'ij', '1lI'])
    {
        $array1 = self::split($string1);
        $array2 = self::split($string2);
        foreach ($array1 as $index => $char1) {
            $char1 = strtolower($char1);
            $char2 = strtolower($array2[$index]);
            $isEqual = false;
            if ($char1 == $char2) {
                $isEqual = true;
            }
            foreach ($array as $row) {
                if (Strings::isContains($row, $char1) && Strings::isContains($row, $char2)) {
                    $isEqual = true;
                    break;
                }
            }
            if (false === $isEqual) {
                break;
            }
        }
        return $isEqual;
    }

    /**
     * 拆分成数组
     *
     * @param array|string $mixed 数组或者字符串
     * @param string $delimiter 分隔符，默认英文逗号（,）
     * @param boolean $combine 是否合并相同元素，默认 false，即不合并
     *
     * @return array
     */
    public static function toArray($mixed, $delimiter = ',', $combine = false)
    {
        $array = [];
        if (is_string($mixed)) {
            $array = explode($delimiter, $mixed);
        } elseif (is_array($mixed)) {
            $array = $mixed;
        }
        if (true === $combine) {
            $array = Arrays::toPart($array);
        }
        return $array;
    }

    /**
     * 返回字符串的子串
     *
     * - 默认获取单个字符
     *
     * @param string $string
     * @param integer $start 起始位置
     * @param integer $length 子串长度，默认为 1
     *
     * @return string
     */
    public static function sub($string, $start, $length = 1)
    {
        return mb_substr($string, $start, $length);
    }

    /**
     * 字符串转数字
     *
     * - 正则为 `/^\d\.*\d*[e|E]/` 的字符串会……，这是 PHP 特性！如果你不喜欢 PHP，右上角
     * - 返回类型根据实际情况定，写成 double 是因为文档生成没法用 number
     *
     * @param string $string
     *
     * @return "double|integer"
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

}
