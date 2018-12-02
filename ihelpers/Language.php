<?php

namespace icy2003\ihelpers;

use icy2003\BaseI;

class Language
{
    protected static $_instance;
    private $__config;
    private $__categories = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }
    /**
     * 创建一个语言单例
     *
     * @return static
     */
    public static function create($config = [])
    {
        if (!static::$_instance instanceof static ) {
            static::$_instance = new static();
            static::$_instance->__config = BaseI::config('Language');
        }

        return static::$_instance;
    }

    public function t($category, $message, $params = [], $language = 'zh-cn')
    {
        $config = $this->__config;
        $language = !empty($language) ? $language : Arrays::value($config, 'language');
        $basePath = Arrays::value($config, 'basePath');
        if (empty($this->__categories[$category . '_' . $language])) {
            $map = include BaseI::getAlias(trim($basePath, '/') . "/{$language}/{$category}.php");
            $this->__categories[$category . '_' . $language] = $map;
        } else {
            $map = $this->__categories[$category . '_' . $language];
        }
        return str_replace(array_map(function ($data) {
            return '{' . $data . '}';
        }, array_keys($params)), array_values($params), Arrays::value($map, $message, $message));
    }
}
