<?php

namespace icy2003\php\iextensions\zetacomponents;

use ezcMailTools as GlobalEzcMailTools;

class ezcMailTools extends GlobalEzcMailTools
{
    public static function mimeDecode($text, $charset = 'utf-8')
    {
        $origtext = $text;
        $text = @iconv_mime_decode($text, 0, $charset);
        if ($text !== false) {
            return $text;
        }
        $text = preg_replace_callback(
            '/=(([a-f][a-f0-9])|([a-f0-9][a-f]))/',
            function ($matches) {
                return strtoupper($matches[0]);
            },
            $origtext
        );
        $text = @iconv_mime_decode($text, 0, $charset);
        if ($text !== false) {
            return $text;
        }
        $text = str_replace(array('?b?', '?q?'), array('?B?', '?Q?'), $origtext);
        $text = @iconv_mime_decode($text, 0, $charset);
        if ($text !== false) {
            return $text;
        }
        $text = preg_replace('/=\?([^?]+)\?/', '=?iso-8859-1?', $origtext);
        $text = @iconv_mime_decode($text, 0, $charset);

        return $text;
    }
}
