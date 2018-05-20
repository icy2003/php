<?php

namespace icy2003\ihelpers;

class Request
{
    private $_headers;

    public function getHeaders()
    {
        if (null === $this->_headers) {
            $this->_headers = new Data();
            if (function_exists('getallheaders')) {
                $headers = getallheaders();
                foreach ($headers as $name => $value) {
                    $this->_headers->add($name, $value);
                }
            } elseif (function_exists('http_get_request_headers')) {
                $headers = http_get_request_headers();
                foreach ($headers as $name => $value) {
                    $this->_headers->add($name, $value);
                }
            } else {
                foreach ($_SERVER as $name => $value) {
                    if (0 === strncmp($name, 'HTTP_', 5)) {
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        $this->_headers->add($name, $value);
                    }
                }
            }
        }

        return $this->_headers;
    }

    public function getMethod()
    {
        if (isset($_POST['_method'])) {
            return strtoupper($_POST['_method']);
        }

        if ($this->headers->has('X-Http-Method-Override')) {
            return strtoupper($this->headers->get('X-Http-Method-Override'));
        }

        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }

        return 'GET';
    }

    public function getIsAjax()
    {
        return 'XMLHttpRequest' === $this->headers->get('X-Requested-With');
    }

    private $_rawBody;

    public function getRawBody()
    {
        if (null === $this->_rawBody) {
            $this->_rawBody = file_get_contents('php://input');
        }

        return $this->_rawBody;
    }

    public function getContentType()
    {
        if (isset($_SERVER['CONTENT_TYPE'])) {
            return $_SERVER['CONTENT_TYPE'];
        }

        //fix bug https://bugs.php.net/bug.php?id=66606
        return $this->headers->get('Content-Type');
    }
}
