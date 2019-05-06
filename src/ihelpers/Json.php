<?php
/**
 * Class Json
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

use icy2003\php\I;

/**
 * Json 类
 */
class Json
{
    /**
     * Json 编码
     *
     * @param array $value 数组
     *
     * @return string
     */
    public static function encode($value)
    {
        return json_encode($value, JSON_PRETTY_PRINT ^ JSON_UNESCAPED_UNICODE);
    }

    /**
     * Json 解码
     *
     * @param string $json Json 字符串
     * @param boolean $assoc 是否返回对象，默认否（和原生函数相反）
     *
     * @return array
     */
    public static function decode($json, $assoc = true)
    {
        return json_decode($json, $assoc);
    }

    /**
     * 判断是否是 Json 字符串
     *
     * @param string $json
     *
     * @return boolean
     */
    public static function isJson($json)
    {
        if (!is_string($json)) {
            return false;
        }
        $array = static::decode($json);
        if (is_array($array)) {
            return true;
        }

        return false;
    }

    /**
     * Json 字符串的 Ajax 返回
     *
     * @param string|array $json 数组或 Json 字符串
     *
     * @return void
     */
    public static function ajax($json)
    {
        header('Content-Type:application/json; charset=utf-8');
        if (is_array($json)) {
            $json = static::encode($json);
        }
        if (false === static::isJson($json)) {
            $json = '[]';
        }
        echo $json;die;
    }

    /**
     * 获取 Json 字符串里的参数
     *
     * @param string $json Json 字符串
     * @param string $key @see \icy2003\php\I::value
     * @param mixed $defaultValue 取不到对应的值时返回的默认值，默认为 null
     *
     * @return mixed
     */
    public static function value($json, $key, $defaultValue = null)
    {
        $array = static::decode($json);
        return I::value($array, $key, $defaultValue);
    }
}
