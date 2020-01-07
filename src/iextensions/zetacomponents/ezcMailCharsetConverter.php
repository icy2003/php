<?php

namespace icy2003\php\iextensions\zetacomponents;

use ezcMailCharsetConverter as GlobalEzcMailCharsetConverter;
use icy2003\php\ihelpers\Charset;

class ezcMailCharsetConverter extends GlobalEzcMailCharsetConverter
{
    public static function convertToUTF8Iconv($text, $originalCharset)
    {
        return Charset::toUtf($text);
    }
}
