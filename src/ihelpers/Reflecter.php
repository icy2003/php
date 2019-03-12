<?php

namespace icy2003\php\ihelpers;

use ReflectionClass;

class Reflecter
{
    protected static $_instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }
    /**
     * @param mixed $object @see http://php.net/manual/zh/reflectionclass.construct.php
     *
     * @return static
     */
    public static function create()
    {
        if (!static::$_instance instanceof static ) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    public function load($object)
    {
        $this->__reflecter = new ReflectionClass($object);
        return $this;
    }

    /**
     *
     * @var \ReflectionClass $reflecter
     */
    private $__reflecter;

    /**
     * @see http://php.net/manual/zh/class.reflectionclass.php
     *
     * @param string $name 反射类可调用的方法
     * @param mixed $arguments 对应的参数
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->__reflecter->$name($arguments);
    }
    /**
     * 获取当前类的所有方法
     *
     * @param callback $callback 对方法名的回调，true 则返回该方法
     * @return array
     */
    public function getCurrentClassMethods($callback = null)
    {
        $callback = null === $callback ? function () {
            return true;
        } : $callback;
        $currentClass = $this->__reflecter->getName();
        $methods = $this->__reflecter->getMethods();
        $result = [];
        foreach ($methods as $method) {
            if ($currentClass === $method->class && $callback($method->name)) {
                $result[] = $method;
            }
        }
        return $result;
    }
}
