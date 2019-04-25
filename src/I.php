<?php
/**
 * Class I
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php;

/**
 * I 类
 */
class I
{
    /**
     * 获取值
     *
     * @param object|array|string $mixed 对象或数组。
     * @param string $keyString 点（.）分割代表层级的字符串，下划线用于对象中转化成驼峰方法，支持数组和对象嵌套。
     *                          对于一个多维数组“$array”来说，“a.b.cd_ef”会拿“$array['a']['b']['cd_ef']”的值。
     *                          如果“$array['a']”是对象，则先检查“getB”方法，然后检查“b”属性。
     *                          如果“$array['a']['b']”是对象，则检查“getCdEf”方法，然后检查“cd_ef”属性。
     * @param mixed $defaultValue 拿不到值时会直接返回该默认值。
     * @return mixed
     */
    public static function value($mixed, $keyString, $defaultValue = null)
    {
        $keyArray = explode('.', $keyString);
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
     * 触发回调
     *
     * @param callback $callback 回调函数
     * @param array $params 回调参数
     * @return mixed
     */
    public static function trigger($callback, $params = [])
    {
        $result = false;
        is_callable($callback) && $result = call_user_func_array($callback, $params);
        return $result;
    }

    /**
     * 定义一个常量
     *
     * @param string $constant 常量名
     * @param mixed $value 值
     *
     * @return void
     */
    public static function def($constant, $value)
    {
        defined($constant) || define($constant, $value);
    }

    /**
     * 让 empty 支持函数调用
     *
     * 注意：此函数并不比 empty 好，只是更方用在回调里
     * 例如：empty($array[0]) 就不能用此函数代替，另外，empty 是语法结构，性能明显比函数高
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
     * 原生函数 count 在非数组情况下，除了 null 会返回 0，其他都返回 1，囧
     *
     * @param array $array 数组
     *
     * @return int
     */
    public static function count($array)
    {
        if (is_array($array)) {
            return count($array);
        }
        return 0;
    }

    /**
     * 获取 php.ini 配置值
     *
     * @param string $key 配置名
     * @param mixed $default 默认值
     *
     * @return mixed
     */
    public static function phpini($key, $default = null)
    {
        if (false !== ($ini = ini_get($key))) {
            return $ini;
        }
        if (false !== ($ini = get_cfg_var($key))) {
            return $ini;
        }
        return $default;
    }

    /**
     * 显示 PHP 错误
     *
     * @param boolean $show 是否显示，默认是
     *
     * @return void
     */
    public static function displayErrors($show = true)
    {
        ini_set("display_errors", true === $show ? 'On' : 'Off');
        true === $show && error_reporting(E_ALL | E_STRICT);
    }

    /**
     * 别名列表
     *
     * @var array
     */
    public static $aliases = [
        '@icy2003/php_tests' => __DIR__ . '/../tests',
        '@icy2003/php' => __DIR__,
    ];

    /**
     * 用别名获取真实路径
     *
     * @param string $alias 别名
     * @param bool $loadNew 是否加载新别名到 I 里，默认否
     *
     * @return string
     */
    public static function getAlias($alias, $loadNew = false)
    {
        if (strncmp($alias, '@', 1)) {
            return $alias;
        }

        $pos = 0;
        while (true) {
            $pos = strpos($alias, '/', $pos);
            $root = $pos === false ? $alias : substr($alias, 0, $pos);
            if (isset(static::$aliases[$root])) {
                if (is_string(static::$aliases[$root])) {
                    return $pos === false ? static::$aliases[$root] : static::$aliases[$root] . substr($alias, $pos);
                } elseif (is_array(static::$aliases[$root])) {
                    foreach (static::$aliases[$root] as $name => $path) {
                        if (strpos($alias . '/', $name . '/') === 0) {
                            return $path . substr($alias, strlen($name));
                        }
                    }
                } else {
                    return false;
                }
            }
            if ($root == $alias) {
                break;
            }
            $pos++;
        }
        // 对 Yii2 的支持
        if (method_exists('\Yii', 'getAlias') && $result = \Yii::getAlias($alias)) {
            true === $loadNew && static::setAlias($alias, $result);
            return $result;
        }

        return false;
    }

    /**
     * 设置别名
     *
     * @param string $alias 别名
     * @param string $path 路径
     *
     * @return void
     */
    public static function setAlias($alias, $path)
    {
        // 对 Yii2 的支持
        method_exists('\Yii', 'getAlias') && \Yii::setAlias($alias, $path);
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
}
