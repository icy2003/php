<?php

namespace icy2003\ihelpers;

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
}
