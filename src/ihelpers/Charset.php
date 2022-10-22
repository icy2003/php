<?php
/**
 * Class Charset
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

/**
 * 编码转换
 */
class Charset
{
    /**
     * 检测字符串的编码格式.
     *
     * @param string $string
     *
     * @return string 编码
     */
    public static function detect($string)
    {
        // EUC-CN 是 GB2312 的表现方式
        $charset = mb_detect_encoding($string, 'UTF-8,EUC-CN,ISO-8859-2,ASCII,UTF-7,EUC-JP,SJIS,eucJP-win,SJIS-win,JIS,ISO-2022-JP', true);

        return $charset;
    }

    /**
     * 将任意编码的字符串转成 UTF-8.
     *
     * @param string $string
     * @param string|null $fromCharset 强制指定编码
     *
     * @return string 转码后的字符串
     */
    public static function toUtf($string, $fromCharset = null)
    {
        return self::convertTo($string, 'UTF-8', $fromCharset);
    }

    /**
     * 将任意编码的字符串转成中文编码.
     *
     * @param string $string
     * @param string|null $fromCharset 强制指定编码
     *
     * @return string 转码后的字符串
     */
    public static function toCn($string, $fromCharset = null)
    {
        return self::convertTo($string, 'EUC-CN', $fromCharset);
    }

    /**
     * 判断字符串是否是 UTF-8 编码
     *
     * @param string $string 待检测的字符串
     *
     * @return boolean
     */
    public static function isUtf8($string)
    {
        return 'UTF-8' === self::detect($string);
    }

    /**
     * 转换编码
     *
     * @param string $string 待转换的字符串
     * @param string $targetCharset 目标编码
     * @param string|null $fromCharset 强制指定编码
     *
     * @return string
     */
    public static function convertTo($string, $targetCharset, $fromCharset = null)
    {
        $charset = null === $fromCharset ? self::detect($string): $fromCharset;
        $converted = @iconv($charset, $targetCharset . '//TRANSLIT//IGNORE', $string);
        // if (false === $converted) {
        //     $converted = mb_convert_encoding($string, $targetCharset);
        // }

        return $converted;
    }
}