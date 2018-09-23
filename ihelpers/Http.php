<?php

namespace icy2003\ihelpers;

class Http
{
    public static function code($code, $message)
    {
        header("HTTP/1.1 {$code} {$message}");
    }

    public static function contentType($content)
    {
        header("Content-Type: {$content}");
    }

    public static function OK()
    {
        self::code(200, 'OK');
    }

    public static function redirect($url, $time = 0)
    {
        header("Refresh: {$time}; url={$url}");
        // header("Location: {$url}");
    }

    public static function utf8()
    {
        self::contentType('text/html; charset=utf-8');
    }

    public static function json()
    {
        self::contentType('application/json');
    }
}
