<?php

namespace icy2003\ihelpers;

use icy2003\ihelpers\Env;

/**
 * @method \icy2003\ihelpers\Link trim(string $str, string $character_mask = " \t\n\r\0\x0B") 去除字符串首尾处的空白字符（或者其他字符）
 * @method \icy2003\ihelpers\Link strlen(string $string) 获取字符串长度
 */
class Link
{

    protected static $_instance;

    protected $_entity;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * 创建单例.
     *
     * @param mixed $entity
     *
     * @return static
     */
    public static function create($entity)
    {
        if (!static::$_instance instanceof static ) {
            static::$_instance = new static();
            static::$_instance->_entity = $entity;
        }
        return static::$_instance;
    }

    /**
     * Undocumented function
     *
     * @param [type] $method
     * @param [type] $arguments
     *
     * @return static
     */
    public function __call($method, $arguments)
    {
        array_unshift($arguments, $this->_entity);
        $this->_entity = Env::trigger($method, $arguments);
        return $this;
    }

    public function result()
    {
        return $this->_entity;
    }
}
