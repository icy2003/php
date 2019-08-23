<?php
/**
 * Class Request
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 * @see https://github.com/yiisoft/yii2/blob/master/framework/web/Request.php
 */
namespace icy2003\php\ihelpers;

use icy2003\php\I;

/**
 * 请求相关
 */
class Request
{

    /**
     * 要检查是否通过 HTTPS 建立连接的头的数组列表
     *
     * 数组键是头名称，数组值是指示安全连接的头值列表
     *
     * 头名称和值的匹配不区分大小写
     *
     * 不建议把不安全的邮件头放在这里
     *
     * @var array
     */
    private $__secureProtocolHeaders = [
        'x-forwarded-proto' => ['https'], // Common
        'front-end-https' => ['on'], // Microsoft
    ];

    /**
     * 代理存储实际客户端 IP 的头列表
     *
     * 不建议把不安全的邮件头放在这里
     *
     * 头名称的匹配不区分大小写
     *
     * @var array
     */
    private $__ipHeaders = [
        'x-forwarded-for', // Common
    ];

    /**
     * 头列表
     *
     * @var array
     */
    private $__headers;

    /**
     * 用于指示请求是 PUT、PATCH、DELETE 的 POST 参数的名称
     *
     * @var string
     */
    private $__methodParam = '_method';

    /**
     * 返回头列表
     *
     * @return array
     */
    public function getHeaders()
    {
        if (null === $this->__headers) {
            if (function_exists('getallheaders')) {
                $headers = getallheaders();
                foreach ($headers as $name => $value) {
                    $this->__headers[strtolower($name)][] = $value;
                }
            } elseif (function_exists('http_get_request_headers')) {
                $headers = http_get_request_headers();
                foreach ($headers as $name => $value) {
                    $this->__headers[strtolower($name)][] = $value;
                }
            } else {
                foreach ($_SERVER as $name => $value) {
                    if (0 === strncmp($name, 'HTTP_', 5)) {
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        $this->__headers[strtolower($name)][] = $value;
                    }
                }
            }
        }
        return $this->__headers;
    }

    /**
     * 返回当前的请求方法，可以是：GET、POST、HEAD、PUT、PATCH、DELETE
     *
     * @return string
     */
    public function getMethod()
    {
        if (isset($_POST[$this->__methodParam]) && !in_array($method = strtoupper($_POST[$this->__methodParam]), ['GET', 'HEAD', 'OPTIONS'])) {
            return $method;
        }
        if ($method = I::get($this->getHeaders(), 'x-http-method-override')) {
            return strtoupper($method);
        }
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }
        return 'GET';
    }

    /**
     * 判断是否是 GET 请求
     *
     * @return boolean
     */
    public function isGet()
    {
        return 'GET' === $this->getMethod();
    }

    /**
     * 判断是否是 OPTIONS 请求
     *
     * @return boolean
     */
    public function isOptions()
    {
        return 'OPTIONS' === $this->getMethod();
    }

    /**
     * 判断是否是 HEAD 请求
     *
     * @return boolean
     */
    public function isHead()
    {
        return 'HEAD' === $this->getMethod();
    }

    /**
     * 判断是否是 POST 请求
     *
     * @return boolean
     */
    public function isPost()
    {
        return 'POST' === $this->getMethod();
    }

    /**
     * 判断是否是 DELETE 请求
     *
     * @return boolean
     */
    public function isDelete()
    {
        return 'DELETE' === $this->getMethod();
    }

    /**
     * 判断是否是 PUT 请求
     *
     * @return boolean
     */
    public function isPut()
    {
        return 'PUT' === $this->getMethod();
    }

    /**
     * 判断是否是 PATCH 请求
     *
     * @return boolean
     */
    public function isPatch()
    {
        return 'PATCH' === $this->getMethod();
    }

    /**
     * 判断是否是 AJAX 请求
     *
     * @return boolean
     */
    public function isAjax()
    {
        return 'XMLHttpRequest' === Arrays::first((array)I::get($this->getHeaders(), 'x-requested-with', []));
    }

    /**
     * 请求体
     *
     * @var string
     */
    private $__rawBody;

    /**
     * 获取请求体
     *
     * @return string
     */
    public function getRawBody()
    {
        if (null === $this->__rawBody) {
            $this->__rawBody = file_get_contents('php://input');
        }
        return $this->__rawBody;
    }

    /**
     * 设置请求体
     *
     * @param string $rawBody
     *
     * @return static
     */
    public function setRawBody($rawBody)
    {
        $this->__rawBody = $rawBody;
        return $this;
    }

    /**
     * 请求参数
     *
     * @var array
     */
    private $__bodyParams;

    /**
     * 返回请求体参数
     *
     * @return array
     */
    public function getBodyParams()
    {
        if (null === $this->__bodyParams) {
            if (isset($_POST[$this->__methodParam])) {
                $this->__bodyParams = $_POST;
                unset($this->__bodyParams[$this->__methodParam]);
            } else {
                $contentType = $this->getContentType();
                if (($pos = strpos($contentType, ';')) !== false) {
                    // application/json; charset=UTF-8
                    $contentType = substr($contentType, 0, $pos);
                }
                if ('application/json' == $contentType) {
                    $this->__bodyParams = Json::decode($this->getRawBody());
                } elseif ('POST' === $this->getMethod()) {
                    $this->__bodyParams = $_POST;
                } else {
                    $this->__bodyParams = [];
                    mb_parse_str($this->getRawBody(), $this->__bodyParams);
                }
            }
        }
        return $this->__bodyParams;
    }

    /**
     * 设置请求体参数
     *
     * @param array $bodyParams
     *
     * @return static
     */
    public function setBodyParams($bodyParams)
    {
        $this->__bodyParams = $bodyParams;
        return $this;
    }

    /**
     * 返回某个请求体参数
     *
     * @param string $name 请求参数名
     * @param mixed $defaultValue 默认值
     *
     * @return mixed
     */
    public function getBodyParam($name, $defaultValue = null)
    {
        return I::get($this->getBodyParams(), $name, $defaultValue);
    }

    /**
     * 返回 POST 请求参数
     *
     * @param string $name POST 参数
     * @param mixed $defaultValue 默认值
     *
     * @return mixed
     */
    public function post($name = null, $defaultValue = null)
    {
        if (null === $name) {
            return $this->getBodyParams();
        }
        return $this->getBodyParam($name, $defaultValue);
    }

    /**
     * GET 参数
     *
     * @var array
     */
    private $__queryParams;

    /**
     * 返回 GET 参数
     *
     * @return array
     */
    public function getQueryParams()
    {
        if (null === $this->__queryParams) {
            return $_GET;
        }
        return $this->__queryParams;
    }

    /**
     * 设置 GET 参数
     *
     * @param array $queryParams
     *
     * @return static
     */
    public function setQueryParams($queryParams)
    {
        $this->__queryParams = $queryParams;
        return $this;
    }

    /**
     * 返回某个 GET 参数
     *
     * @param string $name GET 参数名
     * @param mixed $defaultValue 默认值
     *
     * @return mixed
     */
    public function getQueryParam($name, $defaultValue = null)
    {
        return I::get($this->getQueryParams(), $name, $defaultValue);
    }

    /**
     * 返回 GET 参数
     *
     * @param string $name GET 参数名
     * @param mixed $defaultValue 默认值
     *
     * @return mixed
     */
    public function get($name = null, $defaultValue = null)
    {
        if (null === $name) {
            return $this->getQueryParams();
        }
        return $this->getQueryParam($name, $defaultValue);
    }

    /**
     * 主机
     *
     * @var string
     */
    private $__hostInfo;

    /**
     * 获取主机
     *
     * @return string
     */
    public function getHostInfo()
    {
        if (null === $this->__hostInfo) {
            $secure = $this->isSecureConnection();
            $http = $secure ? 'https' : 'http';
            if (I::get($this->getHeaders(), 'x-forwarded-host')) {
                $this->__hostInfo = $http . '://' . trim((string)Arrays::first(explode(',', (string)Arrays::first((array)I::get($this->getHeaders(), 'x-forward-host', [])))));
            } elseif (I::get($this->getHeaders(), 'host')) {
                $this->__hostInfo = $http . '://' . Arrays::first((array)I::get($this->getHeaders(), 'host'));
            } elseif (isset($_SERVER['SERVER_NAME'])) {
                $this->__hostInfo = $http . '://' . $_SERVER['SERVER_NAME'];
                $port = $secure ? $this->getSecurePort() : $this->getPort();
                if ((80 !== $port && !$secure) || (443 !== $port && $secure)) {
                    $this->__hostInfo .= ':' . $port;
                }
            }
        }
        return $this->__hostInfo;
    }

    /**
     * 主机名
     *
     * @var string
     */
    private $__hostName;

    /**
     * 获取主机名
     *
     * @return string
     */
    public function getHostName()
    {
        if (null === $this->__hostName) {
            $this->__hostName = parse_url($this->getHostInfo(), PHP_URL_HOST);
        }
        return $this->__hostName;
    }

    /**
     * 安全请求的端口号
     *
     * @var int
     */
    private $__securePort;

    /**
     * 获取安全请求的端口号
     *
     * @return int
     */
    public function getSecurePort()
    {
        if (null === $this->__securePort) {
            $serverPort = $this->getServerPort();
            $this->__securePort = $this->isSecureConnection() && null !== $serverPort ? $serverPort : 443;
        }
        return $this->__securePort;
    }

    /**
     * 端口号
     *
     * @var int
     */
    private $__port;

    /**
     * 获取端口号
     *
     * @return int
     */
    public function getPort()
    {
        if (null === $this->__port) {
            $serverPort = $this->getServerPort();
            $this->__port = !$this->isSecureConnection() && null !== $serverPort ? $serverPort : 80;
        }
        return $this->__port;
    }

    /**
     * 获取 GET 字符串
     *
     * @return string
     */
    public function getQueryString()
    {
        return (string) I::get($_SERVER, 'QUERY_STRING', '');
    }

    /**
     * 判断是否是 HTTPS 连接
     *
     * @return boolean
     */
    public function isSecureConnection()
    {
        if (isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)) {
            return true;
        }
        foreach ($this->__secureProtocolHeaders as $header => $values) {
            if (($headerValue = (string) I::get($this->getHeaders(), $header)) !== null) {
                foreach ($values as $value) {
                    if (strcasecmp($headerValue, $value) === 0) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * 获取服务器名
     *
     * @return string
     */
    public function getServerName()
    {
        return (string) I::get($_SERVER, 'SERVER_NAME');
    }

    /**
     * 获取服务器端口
     *
     * @return integer
     */
    public function getServerPort()
    {
        return (int) I::get($_SERVER, 'SERVER_PORT');
    }

    /**
     * 返回 URL 引用
     *
     * @return string
     */
    public function getReferrer()
    {
        return Arrays::first((array) I::get($this->getHeaders(), 'referer'));
    }

    /**
     * 返回 CORS 请求的 URL 源
     *
     * @return string
     */
    public function getOrigin()
    {
        return Arrays::first((array) I::get($this->getHeaders(), 'origin'));
    }

    /**
     * 返回用户代理
     *
     * @return string
     */
    public function getUserAgent()
    {
        return Arrays::first((array) I::get($this->getHeaders(), 'user-agent'));
    }

    /**
     * 返回客户端 IP
     *
     * @return string
     */
    public function getUserIP()
    {
        foreach ($this->__ipHeaders as $ipHeader) {
            if (I::get($this->getHeaders(), $ipHeader)) {
                return trim(Arrays::first(explode(',', Arrays::first((array) I::get($this->getHeaders(), $ipHeader)))));
            }
        }
        return $this->getRemoteIP();
    }

    /**
     * 返回远端 IP
     *
     * @return string
     */
    public function getRemoteIP()
    {
        return (string) I::get($_SERVER, 'REMOTE_ADDR');
    }

    /**
     * 返回此连接另一端的主机名
     *
     * @return string
     */
    public function getRemoteHost()
    {
        return (string) I::get($_SERVER, 'REMOTE_HOST');
    }

    /**
     * 返回请求内容类型
     *
     * @return string
     */
    public function getContentType()
    {
        if (isset($_SERVER['CONTENT_TYPE'])) {
            return $_SERVER['CONTENT_TYPE'];
        }
        return Arrays::first((array) I::get($this->getHeaders(), 'content-type'));
    }

}
