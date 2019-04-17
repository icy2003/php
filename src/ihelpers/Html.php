<?php
/**
 * Class Html
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

/**
 * Html 相关
 */
class Html
{
    /**
     * htmlspecialchars 简化版
     *
     * @see http://php.net/manual/zh/function.htmlspecialchars.php
     *
     * @param string $content HTML 内容
     * @param boolean $doubleEncode 是否重复转化
     * @param string $encoding 编码，默认 UTF-8
     *
     * @return string
     */
    public static function encode($content, $doubleEncode = true, $encoding = 'UTF-8')
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, $encoding, $doubleEncode);
    }

    /**
     * htmlspecialchars_decode 简化版
     *
     * @see http://php.net/manual/zh/function.htmlspecialchars-decode.php
     *
     * @param string $content HTML 内容
     *
     * @return string
     */
    public static function decode($content)
    {
        return htmlspecialchars_decode($content, ENT_QUOTES);
    }
    /**
     * strip_tags 从字符串中去除 HTML 和 PHP 标记
     * 改良：不合法/错误的 html 标签也能匹配
     *
     * @see http://php.net/manual/zh/function.strip-tags.php
     *
     * @param string $html
     * @param array $allowTags 区别于 strip_tags，例如 a 和 h1 标签，strip_tags 的需要写成'<a><h1>'这里就写 ['a', 'h1']
     *
     * @return string
     */
    public static function stripTags($html, $allowTags = [])
    {
        $allowTags = array_map('strtolower', $allowTags);
        return preg_replace_callback('/<\/?([^>\s]+)[^>]*>/i', function ($matches) use (&$allowTags) {
            return in_array(strtolower($matches[1]), $allowTags) ? $matches[0] : '';
        }, $html);
    }
}
