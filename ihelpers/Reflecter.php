<?php

namespace icy2003\ihelpers;

use ReflectionClass;

class Reflecter
{
    protected static $instance;

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
        if (!static::$instance instanceof static) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function load($object)
    {
        $this->reflecter = new ReflectionClass($object);
        return $this;
    }

    /**
     *
     * @var \ReflectionClass $reflecter
     */
    private $reflecter;

    /**
     * @see http://php.net/manual/zh/class.reflectionclass.php
     *
     * @param string $name 反射类可调用的方法
     * @param mixed $arguments 对应的参数
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->reflecter->$name($arguments);
    }
    /**
     * 获取当前类的所有方法
     *
     * @param callback $callback 对方法名的回调，true 则返回该方法
     * @return array
     */
    public function getCurrentClassMethods($callback = null)
    {
        $callback = null === $callback ? function ($name) {
            return true;
        } : $callback;
        $currentClass = $this->reflecter->getName();
        $methods = $this->reflecter->getMethods();
        $result = [];
        foreach ($methods as $method) {
            if ($currentClass === $method->class && $callback($method->name)) {
                $result[] = $method;
            }
        }
        return $result;
    }
}
