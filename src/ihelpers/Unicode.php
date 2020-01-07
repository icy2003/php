<?php
/**
 * Class Unicode
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

/**
 * Unicode 编码/解码
 */
class Unicode
{
    /**
     * Unicode 编码
     *
     * @param string $string
     *
     * @return string
     */
    public static function encode($string)
    {
        return trim(json_encode($string), '"');
    }

    /**
     * Unicode 解码
     *
     * @param string $string
     *
     * @return string
     */
    public static function decode($string)
    {
        return implode('', (array)Json::decode('["' . $string . '"]'));
    }
}
