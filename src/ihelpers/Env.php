<?php

namespace icy2003\php\ihelpers;

class Env
{
    public static function get($configName, $defaultValue = null)
    {
        $result = get_cfg_var($configName);

        return null === $result ? $defaultValue : $result;
    }

    public static function hasExt($extension)
    {
        return extension_loaded($extension);
    }

    public static function isWin()
    {
        return 'WIN' === strtoupper(substr(PHP_OS, 0, 3));
    }

    /**
     * 让 empty 支持函数调用
     * @see http://php.net/manual/zh/function.empty.php
     *
     * @param mixed $data
     * @return boolean
     */
    public static function isEmpty($data)
    {
        return empty($data);
    }

    /**
     * 获取值，以点代替层级，可以是对象嵌套数组
     *
     * @param mixed $mixed
     * @param string $keyString
     * @param mixed $defaultValue
     * @return mixed
     */
    public static function value($mixed, $keyString, $defaultValue = null)
    {
        $keyArray = explode(".", $keyString);
        foreach ($keyArray as $key) {
            if (is_array($mixed)) {
                if (array_key_exists($key, $mixed)) {
                    $mixed = $mixed[$key];
                } else {
                    return $defaultValue;
                }
            } elseif (is_object($mixed)) {
                $method = 'get' . implode('', array_map(function ($part) {
                    return ucfirst(strtolower($part));
                }, explode('_', $key)));
                if (method_exists($mixed, $method)) {
                    $mixed = $mixed->$method();
                } elseif (property_exists($mixed, $key)) {
                    $mixed = $mixed->$key;
                } else {
                    return $defaultValue;
                }
            } else {
                return $defaultValue;
            }
        }
        return $mixed;
    }

    /**
     * 原生函数 basename 会在非 windows 系统区分 `/` 和 `\`，该函数并不会
     *
     * @param string $path
     *
     * @return string
     */
    public static function iBaseName($path, $suffix = null)
    {
        $path = str_replace('\\', '/', $path);
        return basename($path, $suffix);
    }

    /**
     * 原生函数 dirname 会在非 windows 系统区分 `/` 和 `\`，该函数并不会
     *
     * @param string $path
     *
     * @return string
     */
    public static function iDirName($path)
    {
        $path = str_replace('\\', '/', $path);
        return dirname($path);
    }

    /**
     * 触发回调事件
     *
     * @param callback $callback
     * @param array $params
     * @return mixed
     */
    public static function trigger($callback, $params = [])
    {
        $result = false;
        is_callable($callback) && $result = call_user_func_array($callback, $params);
        return $result;
    }

    /**
     * 判断两个回调是否相同
     * 回调格式：
     * 1、'trim'
     * 2、[$a, 'func']
     * 3、['A', 'func']
     * 4、匿名函数（直接返回 false）
     *
     * @param callback $callback1
     * @param callback $callback2
     *
     * @return boolean
     */
    public static function isCallbackEquals($callback1, $callback2)
    {
        if (is_string($callback1) && is_string($callback2)) {
            return $callback1 == $callback2;
        }
        if (is_array($callback1) && is_array($callback2)) {
            if (2 === count($callback1) && 2 === count($callback2)) {
                if (is_string($callback1[0]) && is_string($callback2[0])) {
                    return $callback1[0] == $callback2[0] && $callback1[1] == $callback2[1];
                }
                if (is_object($callback1[0]) && is_object($callback2[0])) {
                    return get_class($callback1[0]) == get_class($callback2[0]) && $callback1[1] == $callback2[1];
                }
            }
        }
        return false;
    }
}
