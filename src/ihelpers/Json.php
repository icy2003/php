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
     * @param int $options Json 选项，默认 JSON_UNESCAPED_UNICODE，其他选项说明：
     * ```
     * JSON_HEX_QUOT：所有的 " 转换成 \u0022。PHP>=5.3.0
     * JSON_HEX_TAG：所有的 < 和 > 转换成 \u003C 和 \u003E。PHP>=5.3.0
     * JSON_HEX_AMP：所有的 & 转换成 \u0026。PHP>=5.3.0
     * JSON_HEX_APOS：所有的 ' 转换成 \u0027。PHP>=5.3.0
     * JSON_NUMERIC_CHECK：将所有数字字符串编码成数字（numbers）。PHP>=5.3.3
     * JSON_PRETTY_PRINT：用空白字符格式化返回的数据。PHP>=5.4.0
     * JSON_UNESCAPED_SLASHES：不要编码 /。PHP>=5.4.0
     * JSON_FORCE_OBJECT：使一个非关联数组输出一个类（Object）而非数组。 在数组为空而接受者需要一个类（Object）的时候尤其有用。PHP>=5.3.0
     * JSON_PRESERVE_ZERO_FRACTION：确保 float 值一直都编码成 float。PHP>=5.6.6
     * JSON_UNESCAPED_UNICODE：以字面编码多字节 Unicode 字符（默认是编码成 \uXXXX）。PHP>=5.4.0
     * JSON_PARTIAL_OUTPUT_ON_ERROR：替换一些不可编码的值，而不是失败。PHP>=5.5.0
     * ```
     * @return string
     */
    public static function encode($value, $options = null)
    {
        null === $options && $options = JSON_UNESCAPED_UNICODE;
        return json_encode($value, $options);
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
     * @param string $key @see \icy2003\php\I::get
     * @param mixed $defaultValue 取不到对应的值时返回的默认值，默认为 null
     *
     * @return mixed
     */
    public static function value($json, $key, $defaultValue = null)
    {
        $array = static::decode($json);
        return I::get($array, $key, $defaultValue);
    }
}
