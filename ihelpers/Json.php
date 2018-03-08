<?php

namespace icy2003\ihelpers;

class Json
{
    public static function encode($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public static function decode($json, $assoc = true)
    {
        return json_decode($json, $assoc);
    }
}
