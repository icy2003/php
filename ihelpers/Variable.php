<?php

namespace icy2003\ihelpers;

class Variable
{
    public static function default($var, $default = null)
    {
        return !empty($var) ? $var : $default;
    }
}
