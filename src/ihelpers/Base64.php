<?php
/**
 * Class Base64
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

/**
 * Base64 相关
 */
class Base64
{

    /**
     * 判断字符串是否是 base64 字符串
     *
     * @param string $string
     *
     * @return boolean
     *
     * @test icy2003\php\tests\ihelpers\Base64Test::testIsBase64
     */
    public static function isBase64($string)
    {
        return $string == base64_encode(base64_decode($string));
    }

    /**
     * base64 编码
     *
     * @param string $string 字符串
     *
     * @return string
     */
    public static function encode($string)
    {
        return base64_encode($string);
    }

    /**
     * base64 解码
     *
     * - 如果给的不是 base64 字符串，则返回 false
     *
     * @param string $string base64 字符串
     *
     * @return string
     */
    public static function decode($string)
    {
        return self::isBase64($string) ? base64_decode($string) : false;
    }

    /**
     * 文件转成 base64 字符串
     *
     * @param string $file 文件路径
     *
     * @return string
     *
     * @test icy2003\php\tests\ihelpers\Base64Test::testFromFile
     */
    public static function fromFile($file)
    {
        $base64 = false;
        file_exists($file) && $base64 = base64_encode(file_get_contents($file));
        return $base64;
    }

    /**
     * base64 字符串转成文件
     *
     * @param string $string base64 字符串
     * @param string $file 文件路径
     *
     * @return boolean
     *
     * @test icy2003\php\tests\ihelpers\Base64Test::testToFile
     */
    public static function toFile($string, $file = null)
    {
        null === $file && $file = './Base64_toFile_' . date('YmdHis') . '.txt';
        return (bool) file_put_contents($file, base64_decode($string));
    }
}
