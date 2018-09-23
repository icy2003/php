<?php

namespace icy2003;

defined('I_DEBUG') || define('I_DEBUG', false);

/**
 * @filename BaseI.php
 * @encoding UTF-8
 *
 * @author icy2003 <2317216477@qq.com>
 *
 * @see https://github.com/icy2003
 */
class BaseI
{
    public static function autoload($className)
    {
        if (false !== strpos($className, '\\')) {
            $classFile = dirname(__DIR__).'/'.str_replace('icy2003', 'icy2003_php', str_replace('\\', '/', $className).'.php');
            if (false === $classFile || !is_file($classFile)) {
                return;
            }
        } else {
            return;
        }
        include $classFile;
        if (I_DEBUG && !class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
            throw new Exception("Unable to find '$className' in file: $classFile. Namespace missing?");
        }
    }

    public static function getVersion()
    {
        return '0.0.1';
    }

    /**
     * 获取一个属性
     * $object 类型：
     * 1：如果是数组，则获取对应键的值
     * 2：如果是字符串并且 $key 为数字，则获取该位置的字符
     * 3：如果是对象，则获取该对象的属性
     * 如果获取不到值，则按照 $defaultValue 给出默认值
     *
     * @param mixed $object
     *
     * @return mixed
     */
    public static function v($object, $key, $defaultValue = null)
    {
        if (is_array($object)) {
            if (array_key_exists($key, $object)) {
                return $object[$key];
            }
        } elseif (is_string($object) && is_numeric($key)) {
            return mb_substr($object, $key, 1);
        } elseif (is_object($object)) {
            return $object->$key;
        }

        return $defaultValue;
    }
}
