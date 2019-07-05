<?php
/**
 * Class Header
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

use icy2003\php\I;

/**
 * 对 header 函数的一些封装
 */
class Header
{
    /**
     * 正常访问
     *
     * HTTP 返回 200
     *
     * @return void
     */
    public static function ok()
    {
        header('HTTP/1.1 200 OK');
    }

    /**
     * 页面不存在
     *
     * HTTP 返回 404
     *
     * @return void
     */
    public static function notFound()
    {
        header('HTTP/1.1 404 Not Found');
    }

    /**
     * 跳转到一个新的地址
     *
     * HTTP 返回 302
     *
     * @param string|null $url 新地址，如果不给这个值，表示刷新当前页面
     * @param integer $time 延迟时间，单位秒
     *
     * @return void
     */
    public static function redirect($url = null, $time = 0)
    {
        null === $url && $url = '';
        if ($time < 0) {
            throw new \Exception('time 参数不能小于 0 ');
        } else {
            header('HTTP/1.1 302 Found');
            header('Refresh: ' . $time . '; ' . $url);
        }
        die;
    }

    /**
     * 永久跳转
     *
     * HTTP 返回 301
     *
     * @param string $url 永久跳转的地址
     *
     * @return void
     */
    public static function redirectPermanently($url)
    {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url);
        die;
    }

    /**
     * 设置网页编码为 UTF-8
     *
     * @return void
     */
    public static function utf8()
    {
        header('Content-Type: text/html; charset=utf-8');
    }

    /**
     * 内容类型为 JSON
     *
     * @return void
     */
    public static function json()
    {
        header('Content-type: application/json');
    }

    /**
     * 内容类型为 XML
     *
     * @return void
     */
    public static function xml()
    {
        header('Content-type: text/xml');
    }

    /**
     * 设置可跨域的域名
     *
     * @param array|string $urls 跨域域名列表，格式例如：http://127.0.0.1:8080
     *
     * @return void
     */
    public static function allowOrigin($urls = '*')
    {
        if ('*' === $urls) {
            header('Access-Control-Allow-Origin:*');
        } else {
            $origin = I::get($_SERVER, 'HTTP_ORIGIN');
            $urls = Strings::toArray($urls);
            if (is_array($urls) && in_array($origin, $urls)) {
                header('Access-Control-Allow-Origin:' . $origin);
            }
        }
        header('Access-Control-Max-Age:86400');
        header('Access-Control-Allow-Credentials:true');
        header('Access-Control-Allow-Methods:GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers:Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With');
    }

    /**
     * 修改 X-Powered-By信息
     *
     * @param string $string
     *
     * @return void
     */
    public static function xPoweredBy($string = 'icy2003')
    {
        header('X-Powered-By: ' . $string);
    }

    /**
     * 告诉浏览器文档内容没有发生改变
     *
     * @return void
     */
    public static function notModified()
    {
        header('HTTP/1.1 304 Not Modified');
    }

    /**
     * 禁用缓存
     *
     * @return void
     */
    public static function noCache()
    {
        header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    }
}
