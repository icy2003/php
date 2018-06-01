<?php

namespace icy2003\ihelpers;

class Response
{
    public static function headerUtf8()
    {
        header('Content-type:text/html;charset=utf-8');
    }
}
