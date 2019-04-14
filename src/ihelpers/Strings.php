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
     * @return int
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
     * @return int
     */
    public static function length($string)
    {
        return mb_strlen($string, 'UTF-8');
    }

    /**
     * 生成随机字符串
     *
     * @param integer $length
     * @param string $chars 字符列表，默认为0-9和大小写字母
     *
     * @return string
     */
    public static function random($length = 32, $chars = self::STRINGS_RANDOM_NUMBER . self::STRINGS_RANDOM_LOWERCASE . self::STRINGS_RANDOM_UPPERCASE)
    {
        $str = '';
        for ($i = 0; $i < $length; ++$i) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
    }

    /**
     * 生成密码 hash
     *
     * @param string $password 原始密码
     * @param int $cost
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
     * @param string $hash
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

        $test .= "\0";
        $hash .= "\0";
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
    public static function startsWith($string, $search)
    {
        return !I::isEmpty($search) && mb_strpos($string, $search) === 0;
    }

    /**
     * 检查字符串是否以某字符串结尾
     *
     * @param string $string
     * @param string $search 待搜索子字符串
     *
     * @return boolean
     */
    public static function endsWith($string, $search)
    {
        return !I::isEmpty($search) && mb_substr($string, -static::length($search)) === $search;
    }

    /**
     * 检查字符串中是否包含某字符串
     *
     * @param string $string
     * @param string $search 待搜索子字符串
     *
     * @return boolean
     */
    public static function contains($string, $search)
    {
        return !I::isEmpty($search) && mb_strpos($string, $search) !== false;
    }

    /**
     * 反转字符串，支持中文
     *
     * @param string $string
     *
     * @return string
     */
    public static function strSplit($string)
    {
        return implode('', array_reverse(preg_split('/(?<!^)(?!$)/u', $string)));
    }

}
