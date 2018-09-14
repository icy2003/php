<?php

namespace icy2003\ihelpers;

class Convert
{
    public static function size($sizeValue)
    {
        $callback = function ($matches) {
            $sizeMap = [
                '' => 0,
                'b' => 0, // 为了简化正则
                'k' => 1,
                'm' => 2,
                'g' => 3,
                't' => 4,
                'p' => 5,
            ];

            return $matches[1] * pow(1024, $sizeMap[strtolower($matches[2])]);
        };

        return preg_replace_callback('/(\d*)([a-z]?)b?/i', $callback, $sizeValue, 1);
    }
}
