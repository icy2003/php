<?php
/**
 * Class Url
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

/**
 * Url 相关
 */
class Url
{
    /**
     * URL 编码
     *
     * 推荐使用 rawurlencode
     *
     * @param string $url
     *
     * @return string
     */
    public static function encode($url)
    {
        return rawurlencode($url);
    }

    /**
     * URL 解码
     *
     * 推荐使用 rawurldecode
     *
     * @param string $url
     *
     * @return string
     */
    public static function decode($url)
    {
        return rawurldecode($url);
    }

    /**
     * 创建 URL 的 query 字符串
     *
     * @param string $queryArray
     *
     * @return string
     */
    public static function buildQuery($queryArray)
    {
        return http_build_query($queryArray, null, '&', PHP_QUERY_RFC3986);
    }
}
