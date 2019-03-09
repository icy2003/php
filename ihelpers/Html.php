<?php

namespace icy2003\php\ihelpers;

class Html
{
    /**
     * htmlspecialchars 简化版
     *
     * @param string $content
     * @param boolean $doubleEncode 是否重复转化
     * @param string $encoding 编码，默认 UTF-8
     * @return string
     */
    public static function encode($content, $doubleEncode = true, $encoding = "UTF-8")
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, $encoding, $doubleEncode);
    }

    /**
     * htmlspecialchars_decode 简化版
     *
     * @param string $content
     * @return string
     */
    public static function decode($content)
    {
        return htmlspecialchars_decode($content, ENT_QUOTES);
    }
    /**
     * strip_tags 改良版：不合法/错误的 html 标签也能匹配
     *
     * @param string $html
     * @param array $allowTags 区别于 strip_tags，例如 a 和 h1 标签，strip_tags 的需要写成'<a><h1>'这里就写 ['a', 'h1']
     * @return string
     */
    public static function stripTags($html, $allowTags = [])
    {
        $allowTags = array_map(strtolower, $allowTags);
        return preg_replace_callback('/<\/?([^>\s]+)[^>]*>/i', function ($matches) use (&$allowTags) {
            return in_array(strtolower($matches[1]), $allowTags) ? $matches[0] : '';
        }, $html);
    }
}