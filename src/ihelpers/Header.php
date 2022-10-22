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
     * 发送原生 HTTP 头
     *
     * - 兼容 Workerman 的 header 头
     *
     * @param string $string 头字符串
     * @param boolean $replace 是否用后面的头替换前面相同类型的头，默认是
     * @param integer $httpResponseCode 强制指定HTTP响应的值，默认不强制
     *
     * @return void
     */
    public static function send($string, $replace = true, $httpResponseCode = null)
    {
        if (method_exists('\Workerman\Protocols\Http', 'header')) {
            call_user_func_array('\Workerman\Protocols\Http::header', [$string, $replace, $httpResponseCode]);
        } else {
            header($string, $replace, $httpResponseCode);
        }
    }

    /**
     * 正常访问
     *
     * HTTP 返回 200
     *
     * @return void
     */
    public static function ok($info = 'OK')
    {
        self::send('HTTP/1.1 200 ' . $info);
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
    public static function redirectPermanently($url, $info = 'Moved Permanently')
    {
        self::send('HTTP/1.1 301 ' . $info);
        self::send('Location: ' . $url);
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
    public static function redirect($url = null, $time = 0, $info = 'Found')
    {
        null === $url && $url = '';
        if ($time < 0) {
            throw new \Exception('time 参数不能小于 0 ');
        } else {
            self::send('HTTP/1.1 302 ' . $info);
            self::send('Refresh: ' . $time . '; ' . $url);
        }
    }

    /**
     * 告诉浏览器文档内容没有发生改变
     *
     * @return void
     */
    public static function notModified($info = 'Not Modified')
    {
        self::send('HTTP/1.1 304 ' . $info);
    }

    /**
     * 禁止访问
     *
     * @return void
     */
    public static function forbidden($info = 'Forbidden')
    {
        self::send('HTTP/1.1 403 ' . $info);
    }

    /**
     * 页面不存在
     *
     * HTTP 返回 404
     *
     * @return void
     */
    public static function notFound($info = 'Not Found')
    {
        self::send('HTTP/1.1 404 ' . $info);
    }

    /**
     * 服务器错误
     *
     * @return void
     */
    public static function serverError($info = 'Internal Server Error')
    {
        self::send('HTTP/1.1 500 ' . $info);
    }

    /**
     * 设置网页编码为 UTF-8
     *
     * @return void
     */
    public static function utf8()
    {
        self::send('Content-Type: text/html; charset=utf-8');
    }

    /**
     * 内容类型为 JSON
     *
     * @return void
     */
    public static function json()
    {
        self::send('Content-Type: application/json');
    }

    /**
     * 内容类型为 XML
     *
     * @return void
     */
    public static function xml()
    {
        self::send('Content-Type: text/xml');
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
            self::send('Access-Control-Allow-Origin:*');
        } else {
            $origin = I::get($_SERVER, 'HTTP_ORIGIN');
            $urls = Strings::toArray($urls);
            if (is_array($urls) && in_array($origin, $urls)) {
                self::send('Access-Control-Allow-Origin:' . $origin);
            }
        }
        self::send('Access-Control-Max-Age:86400');
        self::send('Access-Control-Allow-Credentials:true');
        self::send('Access-Control-Allow-Methods:*');
        self::send('Access-Control-Allow-Headers:Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With');
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
        self::send('X-Powered-By: ' . $string);
    }

    /**
     * 禁用缓存
     *
     * @return void
     */
    public static function noCache()
    {
        self::send('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        self::send('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    }

    /**
     * 设置 X-Frame-Options 选项
     *
     * - 部分浏览器可能不支持，@link https://developer.mozilla.org/zh-CN/docs/Web/HTTP/X-Frame-Options
     *
     * @param boolean|string $asFrame 取值如下：
     * - false：X-Frame-Options: deny 表示该页面不允许在 frame 中展示，即便是在相同域名的页面中嵌套也不允许
     * - true：X-Frame-Options: sameorigin 表示该页面可以在相同域名页面的 frame 中展示
     * - 字符串：X-Frame-Options: allow-from https://example.com/ 表示该页面可以在指定来源的 frame 中展示
     *
     * @return void
     */
    public static function frame($asFrame = true)
    {
        if (true === $asFrame) {
            self::send('X-Frame-Options: sameorigin');
        } elseif (false === $asFrame) {
            self::send('X-Frame-Options: deny');
        } else {
            $asFrame = (string) $asFrame;
            self::send('X-Frame-Options: allow-from ' . $asFrame);
        }
    }

    /**
     * 是否禁用 mine 嗅探
     *
     * - 部分浏览器不支持，@link https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Headers/X-Content-Type-Options
     * - 下面两种情况的请求将被阻止：
     *      1. 请求类型是"style" 但是 MIME 类型不是 "text/css"，
     *      2. 请求类型是"script" 但是 MIME 类型不是
     *
     * @param boolean $disabled 默认禁用
     *
     * @return void
     */
    public static function mimeSniffing($disabled = true)
    {
        if (true === $disabled) {
            self::send('X-Content-Type-Options: nosniff');
        }
    }
}
