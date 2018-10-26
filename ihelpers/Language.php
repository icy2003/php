<?php

namespace icy2003\ihelpers;

use icy2003\BaseI;

class Language
{
    public static function t($category, $message, $params = [], $language = 'zh-cn')
    {
        $config = BaseI::config('Language');
        $language = !empty($language) ? $language : Arrays::value($config, 'language');
        $basePath = Arrays::value($config, 'basePath');
        $map = require BaseI::getAlias(trim($basePath, '/') . "/{$language}/{$category}.php");
        return str_replace(array_map(function ($data) {
            return '{' . $data . '}';
        }, array_keys($params)), array_values($params), Arrays::value($map, $message));
    }
}