<?php

namespace icy2003\php\ihelpers;

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

    public static function isJson($json)
    {
        if (!is_string($json)) {
            return false;
        }
        $array = self::decode($json);
        if (is_array($array)) {
            return true;
        }

        return false;
    }
}
