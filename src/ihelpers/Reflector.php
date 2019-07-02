<?php
/**
 * Class Reflector
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

use ReflectionClass;

/**
 * 反射类扩展
 */
class Reflector
{

    /**
     * 加载一个类对象
     *
     * @param object $object
     */
    public function __construct($object)
    {
        $this->__reflector = new ReflectionClass($object);
    }

    /**
     *
     * 反射对象
     *
     * @var \ReflectionClass $__reflector
     */
    private $__reflector;

    /**
     *
     * ReflectionClass 类调用方法
     *
     * @see http://php.net/manual/zh/class.reflectionclass.php
     *
     * @param string $name 反射类可调用的方法
     * @param mixed $arguments 对应的参数
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->__reflector->$name($arguments);
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
        $currentClass = $this->__reflector->getName();
        $methods = $this->__reflector->getMethods();
        $result = [];
        foreach ($methods as $method) {
            if ($currentClass === $method->class && $callback($method->name)) {
                $result[] = $method;
            }
        }
        return $result;
    }
}
