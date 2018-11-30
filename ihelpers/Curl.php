<?php

namespace icy2003\ihelpers;

/**
 * CURL 类
 * 年久失修，已不想维护，如需要 HTTP 相关操作，请转至：https://github.com/guzzle/guzzle
 * 该项目评星在 `HTTP` 搜索条件下，碾压第二名 wordpress 出的库。大佬的风采也只能 orz
 *
 * @filename Curl.php
 * @encoding UTF-8
 * @deprecated
 *
 * @author icy2003 <2317216477@qq.com>
 */
class Curl
{
    // CURL 选项
    private $_options = [];

    // CURL 选项默认配置
    private $_config = [
        CURLOPT_RETURNTRANSFER => true, // 返回页面内容
        CURLOPT_HEADER => false, // 不返回头部
        CURLOPT_ENCODING => '', // 处理所有编码
        CURLOPT_USERAGENT => 'spider',
        CURLOPT_AUTOREFERER => true, // 自定重定向
        CURLOPT_CONNECTTIMEOUT => 30, // 链接超时时间
        CURLOPT_TIMEOUT => 30, // 超时时间
        CURLOPT_MAXREDIRS => 10, // 超过十次重定向后停止
        CURLOPT_SSL_VERIFYHOST => false, // 不检查ssl链接
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_VERBOSE => true,
    ];
    private $_error = null;
    private $_header = null;
    private $_info = null;
    private $_status = null;
    private $_debug = false;

    public function __construct($options = [], $debug = false)
    {
        $this->_options = $options + $this->_config;
        $this->_debug = $debug;
    }

    public function getOptions()
    {
        return $this->_options;
    }

    public function setOption($key, $value)
    {
        $this->_options[$key] = $value;

        return $this;
    }

    public function setOptions($options)
    {
        $this->_options = $options + $this->_options;

        return $this;
    }

    public function buildUrl($url, $param)
    {
        if (empty($param)) {
            return $url;
        }

        return $url.(strpos($url, '?') ? '&' : '?').http_build_query($param);
    }

    /**
     * 执行一个 CURL 请求
     *
     * @param string $url
     * @param array  $options
     *
     * @return string
     */
    public function exec($url, $options = [])
    {
        $ch = curl_init($url);
        $options = $options + $this->getOptions();
        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        $this->_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (false === $output) {
            $this->_error = curl_error($ch);
            $this->_info = curl_getinfo($ch);
        } elseif (true === $this->_debug) {
            $this->_info = curl_getinfo($ch);
        }
        if (isset($options[CURLOPT_HEADER]) && true === $options[CURLOPT_HEADER]) {
            list($header, $output) = $this->_processHeader($output, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            $this->_header = $header;
        }
        curl_close($ch);

        return $output;
    }

    private function _processHeader($response, $headerSize)
    {
        return [substr($response, 0, $headerSize), substr($response, $headerSize)];
    }

    /**
     * GET 请求
     *
     * @param string $url
     * @param array  $params
     *
     * @return string
     */
    public function get($url, $params = [])
    {
        $execUrl = $this->buildUrl($url, $params);

        return $this->exec($execUrl);
    }

    /**
     * POST 请求
     *
     * @param string $url
     * @param array  $postBody
     * @param array  $params   选填的 url 上的参数，可以直接拼在 url 上或者放在这里
     *
     * @return string
     */
    public function post($url, $postBody = [], $params = [])
    {
        $execUrl = $this->buildUrl($url, $params);
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = $postBody;
        $this->setOptions($options);

        return $this->exec($execUrl);
    }

    /**
     * PUT 请求
     * 用 POST 替代，添加 `_method` 字段.
     *
     * @param string $url
     * @param array  $putBody
     * @param array  $params  选填的 url 上的参数，可以直接拼在 url 上或者放在这里
     *
     * @return string
     */
    public function put($url, $putBody = [], $params = [])
    {
        $execUrl = $this->buildUrl($url, $params);
        $options[CURLOPT_POST] = true;
        $putBody['_method'] = 'PUT';
        $options[CURLOPT_POSTFIELDS] = $putBody;
        $this->setOptions($options);

        return $this->exec($execUrl);
    }

    /**
     * DELETE 请求
     * 用 POST 替代，添加 `_method` 字段.
     *
     * @param string $url
     * @param array  $deleteBody
     * @param array  $params     选填的 url 上的参数，可以直接拼在 url 上或者放在这里
     *
     * @return string
     */
    public function delete($url, $deleteBody = [], $params = [])
    {
        $execUrl = $this->buildUrl($url, $params);
        $options[CURLOPT_POST] = true;
        $deleteBody['_method'] = 'DELETE';
        $options[CURLOPT_POSTFIELDS] = $deleteBody;
        $this->setOptions($options);

        return $this->exec($execUrl);
    }

    /**
     * 获取错误信息.
     */
    public function getError()
    {
        return $this->_error;
    }

    public function getInfo()
    {
        return $this->_info;
    }

    public function getStatus()
    {
        return $this->_status;
    }
}
