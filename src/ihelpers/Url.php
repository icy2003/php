<?php
/**
 * Class Url
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

use icy2003\php\I;

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

    /**
     * 拼装 URL 链接
     *
     *
     * @param array|string $url URL 信息数组
     * - 数组：第一个项为路由，其他项示键值对的参数
     * - 字符串：即为路由
     * @param false|string $scheme 协议名，默认 false，表示使用相对路径
     *
     * @return string
     */
    public static function to($url, $scheme = false)
    {
        if (I::isYii2()) {
            return (string)I::call(['\yii\helpers\Url', 'to'], [$url, $scheme]);
        }
        $request = new Request();
        if (is_array($url)) {
            $params = $url;
            $anchor = (string) I::get($url, '#', '');
            unset($params['#']);
            $route = trim($params[0], '/');
            unset($params[0]);
            $baseUrl = $request->getBaseUrl();
            $url = $baseUrl . '/' . $route . '?' . self::buildQuery($params) . '#' . $anchor;
            if (false !== $scheme) {
                if (strpos($url, '://') === false) {
                    $hostInfo = $request->getHostInfo();
                    if (strncmp($url, '//', 2) === 0) {
                        $url = substr($hostInfo, 0, strpos($hostInfo, '://')) . ':' . $url;
                    } else {
                        $url = $hostInfo . $url;
                    }
                }
                return self::scheme($url, $scheme);
            }
            return $url;
        } else {
            $url = (string)I::getAlias($url);
            if (false === $scheme) {
                return $url;
            }
            if (self::isRelative($url)) {
                $url = $request->getHostInfo() . '/' . ltrim($url);
                return '' === $scheme ? $url : $scheme . '://' . ltrim($url, '/');
            }
            return self::scheme($url, $scheme);
        }
    }

    /**
     * 判断 url 是否是相对地址
     *
     * @param string $url
     *
     * @return boolean
     */
    public static function isRelative($url)
    {
        return strncmp($url, '//', 2) && strpos($url, '://') === false;
    }

    /**
     * 获取加协议的 url
     *
     * @param string $url
     * @param string|false $scheme
     *
     * @return string
     */
    public static function scheme($url, $scheme)
    {
        if (self::isRelative($url) || !is_string($scheme)) {
            return $url;
        }

        if (Strings::isStartsWith($url, '//')) {
            return $scheme === '' ? $url : "$scheme:$url";
        }

        if (Strings::isContains($url, '://', $pos)) {
            if ($scheme === '') {
                $url = substr($url, $pos + 1);
            } else {
                $url = $scheme . substr($url, $pos);
            }
        }

        return $url;
    }
}
