<?php

namespace icy2003\php;

use icy2003\php\ihelpers\Arrays;

defined('I_DEBUG') || define('I_DEBUG', false);
$defaultConfig = '@icy2003/config.php';
defined("I_DEFAULT_CONFIG_FILE") || define("I_DEFAULT_CONFIG_FILE", $defaultConfig);
defined("I_CONFIG_FILE") || define("I_CONFIG_FILE", $defaultConfig);

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
    public static $aliases = [
        '@icy2003' => __DIR__,
    ];
    public static function autoload($className)
    {

        if (false !== strpos($className, '\\')) {
            $classFile = static::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
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

    public static function getAlias($alias)
    {
        if (strncmp($alias, '@', 1)) {
            return $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (isset(static::$aliases[$root])) {
            if (is_string(static::$aliases[$root])) {
                return $pos === false ? static::$aliases[$root] : static::$aliases[$root] . substr($alias, $pos);
            }

            foreach (static::$aliases[$root] as $name => $path) {
                if (strpos($alias . '/', $name . '/') === 0) {
                    return $path . substr($alias, strlen($name));
                }
            }
        }

        return false;
    }

    public static function setAlias($alias, $path)
    {
        if (strncmp($alias, '@', 1)) {
            $alias = '@' . $alias;
        }
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);
        if ($path !== null) {
            $path = strncmp($path, '@', 1) ? rtrim($path, '\\/') : static::getAlias($path);
            if (!isset(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [$alias => $path];
                }
            } elseif (is_string(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [
                        $alias => $path,
                        $root => static::$aliases[$root],
                    ];
                }
            } else {
                static::$aliases[$root][$alias] = $path;
                krsort(static::$aliases[$root]);
            }
        } elseif (isset(static::$aliases[$root])) {
            if (is_array(static::$aliases[$root])) {
                unset(static::$aliases[$root][$alias]);
            } elseif ($pos === false) {
                unset(static::$aliases[$root]);
            }
        }
    }

    public static function config($name)
    {
        return Arrays::value(Arrays::arrayMergeRecursive(require static::getAlias(I_DEFAULT_CONFIG_FILE), require static::getAlias(I_CONFIG_FILE)), $name);
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
