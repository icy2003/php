<?php

namespace icy2003\php\ihelpers;

use icy2003\php\I;

/**
 * @see https://github.com/yiisoft/yii2/blob/master/framework/web/Request.php
 */
class Request
{

    private $__secureProtocolHeaders = [
        'x-forwarded-proto' => ['https'], // Common
         'front-end-https' => ['on'], // Microsoft
    ];

    private $__ipHeaders = [
        'x-forwarded-for', // Common
    ];

    private $__headers;
    private $__methodParam = '_method';

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

    public function getMethod()
    {
        if (isset($_POST[$this->__methodParam]) && !in_array($method = strtoupper($_POST[$this->__methodParam]), ['GET', 'HEAD', 'OPTIONS'])) {
            return $method;
        }
        if ($method = I::value($this->getHeaders(), 'x-http-method-override')) {
            return strtoupper($method);
        }
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }
        return 'GET';
    }

    public function getIsGet()
    {
        return 'GET' === $this->getMethod();
    }

    public function getIsOptions()
    {
        return 'OPTIONS' === $this->getMethod();
    }

    public function getIsHead()
    {
        return 'HEAD' === $this->getMethod();
    }

    public function getIsPost()
    {
        return 'POST' === $this->getMethod();
    }

    public function getIsDelete()
    {
        return 'DELETE' === $this->getMethod();
    }

    public function getIsPut()
    {
        return 'PUT' === $this->getMethod();
    }

    public function getIsPatch()
    {
        return 'PATCH' === $this->getMethod();
    }

    public function getIsAjax()
    {
        return 'XMLHttpRequest' === Arrays::first(I::value($this->getHeaders(), 'x-requested-with'));
    }

    private $__rawBody;

    public function getRawBody()
    {
        if (null === $this->__rawBody) {
            $this->__rawBody = file_get_contents('php://input');
        }
        return $this->__rawBody;
    }

    private $__bodyParams;

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
                    $params = Json::decode($this->getRawBody());
                    $this->__bodyParams = null === $params ? [] : $params;
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

    public function getBodyParam($name, $defaultValue = null)
    {
        return I::value($this->getBodyParams(), $name, $defaultValue);
    }

    public function post($name = null, $defaultValue = null)
    {
        if (null === $name) {
            return $this->getBodyParams();
        }
        return $this->getBodyParam($name, $defaultValue);
    }

    private $__queryParams;

    public function getQueryParams()
    {
        if (null === $this->__queryParams) {
            return $_GET;
        }
        return $this->__queryParams;
    }

    public function getQueryParam($name, $defaultValue = null)
    {
        return I::value($this->getQueryParams(), $name, $defaultValue);
    }

    public function get($name = null, $defaultValue = null)
    {
        if (null === $name) {
            return $this->getQueryParams();
        }
        return $this->getQueryParam($name, $defaultValue);
    }

    private $__hostInfo;

    public function getHostInfo()
    {
        if (null === $this->__hostInfo) {
            $secure = $this->getIsSecureConnection();
            $http = $secure ? 'https' : 'http';
            if (I::value($this->getHeaders(), 'x-forwarded-host')) {
                $this->__hostInfo = $http . '://' . trim(Arrays::first(explode(',', Arrays::first(I::value($this->getHeaders(), 'x-forward-host')))));
            } elseif (I::value($this->getHeaders(), 'host')) {
                $this->__hostInfo = $http . '://' . Arrays::first(I::value($this->getHeaders(), 'host'));
            } elseif (isset($_SERVER['SERVER_NAME'])) {
                $this->__hostInfo = $http . '://' . $_SERVER['SERVER_NAME'];
                $port = $secure ? $this->getSecurePort() : $this->getPort();
                if (80 !== $port && !$secure || 443 !== $port && $secure) {
                    $this->__hostInfo .= ':' . $port;
                }
            }
        }
        return $this->__hostInfo;
    }

    private $__hostName;

    public function getHostName()
    {
        if (null === $this->__hostName) {
            $this->__hostName = parse_url($this->getHostInfo(), PHP_URL_HOST);
        }
        return $this->__hostName;
    }

    private $__securePort;

    public function getSecurePort()
    {
        if (null === $this->__securePort) {
            $serverPort = $this->getServerPort();
            $this->__securePort = $this->getIsSecureConnection() && null !== $serverPort ? $serverPort : 443;
        }
        return $this->__securePort;
    }

    private $__port;

    public function getPort()
    {
        if (null === $this->__port) {
            $serverPort = $this->getServerPort();
            $this->__port = !$this->getIsSecureConnection() && null !== $serverPort ? $serverPort : 80;
        }
        return $this->__port;
    }

    public function getQueryString()
    {
        return I::value($_SERVER, 'QUERY_STRING', '');
    }

    public function getIsSecureConnection()
    {
        if (isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)) {
            return true;
        }
        foreach ($this->__secureProtocolHeaders as $header => $values) {
            if (($headerValue = I::value($this->getHeaders(), $header)) !== null) {
                foreach ($values as $value) {
                    if (strcasecmp($headerValue, $value) === 0) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function getServerName()
    {
        return I::value($_SERVER, 'SERVER_NAME');
    }

    public function getServerPort()
    {
        return I::value($_SERVER, 'SERVER_PORT');
    }

    public function getReferrer()
    {
        return Arrays::first(I::value($this->getHeaders(), 'referer'));
    }

    public function getOrigin()
    {
        return Arrays::first(I::value($this->getHeaders(), 'origin'));
    }

    public function getUserAgent()
    {
        return Arrays::first(I::value($this->getHeaders(), 'user-agent'));
    }

    public function getUserIP()
    {
        foreach ($this->__ipHeaders as $ipHeader) {
            if (I::value($this->getHeaders(), $ipHeader)) {
                return trim(Arrays::first(explode(',', Arrays::first(I::value($this->getHeaders(), $ipHeader)))));
            }
        }
        return $this->getRemoteIP();
    }

    public function getRemoteIP()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }

    public function getRemoteHost()
    {
        return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null;
    }

    public function getContentType()
    {
        if (isset($_SERVER['CONTENT_TYPE'])) {
            return $_SERVER['CONTENT_TYPE'];
        }
        return Arrays::first(I::value($this->getHeaders(), 'content-type'));
    }

}
