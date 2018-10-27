<?php

namespace icy2003\ihelpers;

use Exception;

class Strings
{

    public static function byteLength($string)
    {
        return mb_strlen($string, '8bit');
    }

    public static function random($length = 32)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $str = '';
        for ($i = 0; $i < $length; ++$i) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
    }

    public static function generatePasswordHash($password, $cost = null)
    {
        null === $cost && $cost = 13;
        // PHP 5 >= 5.5.0, PHP 7
        if (function_exists('password_hash')) {
            return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
        }
        if ($cost < 4 || $cost > 31) {
            throw new Exception('cost 必须大于等于 4，小于等于 31');
        }
        $salt = sprintf('$2y$%02d$', $cost);
        $salt .= str_replace('+', '.', substr(base64_encode(static::random(20)), 0, 22));
        $hash = crypt($password, $salt);
        if (!is_string($hash) || strlen($hash) !== 60) {
            throw new Exception('未知错误');
        }
        return $hash;
    }

    public static function validatePassword($password, $hash)
    {
        if (!is_string($password) || $password === '') {
            throw new Exception('password 必须是字符串且不能为空');
        }
        if (!preg_match('/^\$2[axy]\$(\d\d)\$[\.\/0-9A-Za-z]{22}/', $hash, $matches)
            || $matches[1] < 4
            || $matches[1] > 30) {
            throw new Exception('hash 不合法');
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

}
