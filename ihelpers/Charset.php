<?php

namespace icy2003\php\ihelpers;

/**
 * 编码转换.
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
        $charset = mb_detect_encoding($string, 'EUC-CN, UTF-8, ISO-8859-2, ASCII, UTF-7, EUC-JP, SJIS, eucJP-win, SJIS-win, JIS, ISO-2022-JP', true);
        // `mb_detect_encoding` 会把 `WINDOWS-1250` 处理成 `ISO-8859-2`
        if ('ISO-8859-2' === $charset && preg_match('~[\x7F-\x9F\xBC]~', $string)) {
            $charset = 'WINDOWS-1250';
        }

        return $charset;
    }

    /**
     * 将任意编码的字符串转成 UTF-8.
     *
     * @param string $string
     *
     * @return string 转码后的字符串
     */
    public static function convert2utf($string, $charset = '')
    {
        $charset = $charset ?: static::detect($string);
        $converted = @iconv($charset, 'UTF-8//TRANSLIT//IGNORE', $string);
        if (false === $converted) {
            $converted = mb_convert_encoding($string, 'UTF-8');
        }

        return $converted;
    }

    /**
     * 将任意编码的字符串转成中文编码.
     *
     * @param string $string
     *
     * @return string 转码后的字符串
     */
    public static function convert2cn($string, $charset = '')
    {
        $charset = $charset ?: static::detect($string);
        $converted = @iconv($charset, 'EUC-CN//TRANSLIT//IGNORE', $string);
        if (false === $converted) {
            $converted = mb_convert_encoding($string, 'EUC-CN');
        }

        return $converted;
    }

    public static function isUtf8($string)
    {
        return 'UTF-8' === static::detect($string);
    }
}
