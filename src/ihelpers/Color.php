<?php

namespace icy2003\php\ihelpers;

class Color
{
    public static function hex2rgb($hex)
    {
        $r = ($hex >> 16) & 0xFF;
        $g = ($hex >> 8) & 0xFF;
        $b = $hex & 0xFF;
        var_dump([$r, $g, $b]);
    }
}

Color::hex2rgb('222222');
