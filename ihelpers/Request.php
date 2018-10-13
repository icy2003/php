<?php

namespace icy2003\ihelpers;

class Request
{
    private static $instance;
    private $headers;
    private $rawBody;

    private function __construct()
    {
    }

    private function __clone()
    {
    }
    /**
     * 创建一个 Request 对象
     *
     * @return static
     */
    public static function create()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
            if (null === self::$instance->headers) {
                // 以 apache 模块方式运行时支持
                if (function_exists('getallheaders')) {
                    $headers = getallheaders();
                    foreach ($headers as $name => $value) {
                        self::$instance->headers[$name][] = $value;
                    }
                } elseif (function_exists('http_get_request_headers')) {
                    $headers = http_get_request_headers();
                    foreach ($headers as $name => $value) {
                        self::$instance->headers[$name][] = $value;
                    }
                } else {
                    foreach ($_SERVER as $name => $value) {
                        if (0 === strncmp($name, 'HTTP_', 5)) {
                            $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                            self::$instance->headers[$name][] = $value;
                        }
                    }
                }
            }
            if (null === self::$instance->rawBody) {
                self::$instance->rawBody = file_get_contents('php://input');
            }

        }

        return self::$instance;
    }
    public function getHeaders()
    {
        return $this->headers;
    }


    public function getRawBody()
    {
        return $this->rawBody;
    }

    public function getMethod()
    {
        if (isset($_POST['_method'])) {
            return strtoupper($_POST['_method']);
        }

        if (array_key_exists('X-Http-Method-Override', $this->headers)) {
            return strtoupper(implode($this->headers['X-Http-Method-Override']));
        }

        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }

        return 'GET';
    }
}
