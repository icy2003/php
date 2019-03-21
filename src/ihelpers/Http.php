<?php

namespace icy2003\php\ihelpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * 用 guzzlehttp 封装的简单的方法，更复杂的请使用 guzzlehttp
 */
class Http
{
    /**
     * 发送一个 GET 请求
     *
     * @param string $url 请求地址
     * @param array $get GET 参数
     * @param array $options 额外参数，默认不检测 https
     *
     * @return string
     */
    public static function get($url, $get = [], $options = [])
    {
        $client = new Client(Arrays::arrayMergeRecursive(['verify' => false], $options));
        $response = $client->request('GET', $url, [
            'query' => $get,
        ]);
        return $response->getBody()->getContents();
    }

    /**
     * 发送一个异步的 GET 请求
     *
     * @param string $url 请求地址
     * @param array $get GET 参数
     * @param array $options 额外参数，默认不检测 https
     * @param callback $success 成功时的回调 function($res1, $res2) $res1 是内容，$res2 是结果对象
     * @param callback $error 失败时的回调 function($res) $res 是结果对象
     *
     * @return void
     */
    public static function getAsync($url, $get = [], $options = [], $success = null, $error = null)
    {
        $client = new Client(Arrays::arrayMergeRecursive(['verify' => false], $options));
        $promise = $client->requestAsync('GET', $url, [
            'query' => $get,
        ]);
        $promise->then(function (ResponseInterface $res) use ($success) {
            $success && $success($res->getBody()->getContents(), $res);
        }, function (RequestException $err) use ($error) {
            $error && $error($err);
        });
    }

    /**
     * 发送一个表单 POST 请求
     *
     * @param string $url 请求地址
     * @param array $post POST 参数
     * @param array $get GET 参数
     * @param array $options 额外参数，默认不检测 https
     *
     * @return string
     */
    public static function post($url, $post = [], $get = [], $options = [])
    {
        $client = new Client(Arrays::arrayMergeRecursive(['verify' => false], $options));
        $response = $client->request('POST', $url, [
            'query' => $get,
            'form_params' => $post,
        ]);
        return $response->getBody()->getContents();
    }

    /**
     * 发送一个异步的表单 POST 请求
     *
     * @param string $url 请求地址
     * @param array $post POST 参数
     * @param array $get GET 参数
     * @param array $options 额外参数，默认不检测 https
     * @param callback $success 成功时的回调 function($res1, $res2) $res1 是内容，$res2 是结果对象
     * @param callback $error 失败时的回调 function($res) $res 是结果对象
     *
     * @return void
     */
    public static function postAsync($url, $post = [], $get = [], $options = [], $success = null, $error = null)
    {
        $client = new Client(Arrays::arrayMergeRecursive(['verify' => false], $options));
        $promise = $client->requestAsync('POST', $url, [
            'query' => $get,
            'form_params' => $post,
        ]);
        $promise->then(function (ResponseInterface $res) use ($success) {
            $success && $success($res->getBody()->getContents(), $res);
        }, function (RequestException $err) use ($error) {
            $error && $error($err);
        });
    }

    /**
     * 发送一个文本 POST 请求
     *
     * @param string $url 请求地址
     * @param string $body POST 文本
     * @param array $get GET 参数
     * @param array $options 额外参数，默认不检测 https
     *
     * @return string
     */
    public static function body($url, $body = '', $get = [], $options = [])
    {
        $client = new Client(Arrays::arrayMergeRecursive(['verify' => false], $options));
        $response = $client->request('POST', $url, [
            'query' => $get,
            'body' => $body,
        ]);
        return $response->getBody()->getContents();
    }

    /**
     * 发送一个异步的文本 POST 请求
     *
     * @param string $url 请求地址
     * @param string $body POST 文本
     * @param array $get GET 参数
     * @param array $options 额外参数，默认不检测 https
     * @param callback $success 成功时的回调 function($res1, $res2) $res1 是内容，$res2 是结果对象
     * @param callback $error 失败时的回调 function($res) $res 是结果对象
     *
     * @return void
     */
    public static function bodyAsync($url, $body = '', $get = [], $options = [], $success = null, $error = null)
    {
        $client = new Client(Arrays::arrayMergeRecursive(['verify' => false], $options));
        $promise = $client->requestAsync('POST', $url, [
            'query' => $get,
            'body' => $body,
        ]);
        $promise->then(function (ResponseInterface $res) use ($success) {
            $success && $success($res->getBody()->getContents(), $res);
        }, function (RequestException $err) use ($error) {
            $error && $error($err);
        });
    }
}
