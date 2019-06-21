<?php
/**
 * Class I
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php;

use Exception;
use icy2003\php\icomponents\file\LocalFile;
use icy2003\php\ihelpers\Strings;
use ReflectionClass;

/**
 * I 类
 */
class I
{

    /**
     * 获取值
     *
     * @param object|array $mixed 对象或数组。
     * @param string $keyString 点（.）分割代表层级的字符串，下划线用于对象中转化成驼峰方法，支持数组和对象嵌套。
     * - 对于一个多维数组 `$array` 来说，`a.b.cd_ef` 会拿 `$array['a']['b']['cd_ef']` 的值。
     * - 如果 `$array['a']` 是对象，则先检查 `getB` 方法，然后检查 `b`属性。
     * - 如果 `$array['a']['b']` 是对象，则检查 `getCdEf` 方法，然后检查 `cd_ef` 属性。
     * @param mixed $defaultValue 拿不到值时会直接返回该默认值。
     *
     * @return mixed
     */
    public static function get($mixed, $keyString, $defaultValue = null)
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
                $method = 'get' . ucfirst(Strings::underline2camel($key));
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
     * 设置值
     *
     * @param array|object $mixed 对象或数组
     * @param string $key 键
     * @param mixed $value 值
     * @param boolean $overWrite 如果对应的值存在，是否用给定的值覆盖，默认 true，即：是
     *
     * @return void
     */
    public static function set(&$mixed, $key, $value, $overWrite = true)
    {
        if (is_array($mixed)) {
            if (false === isset($mixed[$key]) || true === $overWrite) {
                $mixed[$key] = $value;
            }
        } elseif (is_object($mixed)) {
            $originValue = self::get($mixed, $key);
            if (null === $originValue || true === $overWrite) {
                $method = 'set' . ucfirst(Strings::underline2camel($key));
                if (method_exists($mixed, $method)) {
                    $mixed->$method($value);
                } elseif (property_exists($mixed, $key)) {
                    $mixed->$key = $value;
                }
            }
        }
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
     * 注意：此函数并不比 empty 好，只是为了让 empty 支持函数调用
     *
     * 例如：empty($array[0]) 就不能用此函数代替，另外，empty 是语法结构，性能明显比函数高
     *
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
    public static $aliases = [];

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
        $localFile = new LocalFile();
        $aliases = [
            '@vendor' => __DIR__ . '/../../../../vendor',
            '@icy2003/php_tests' => __DIR__ . '/../tests',
            '@icy2003/php_runtime' => __DIR__ . '/../runtime',
            '@icy2003/php' => __DIR__,
        ];
        foreach ($aliases as $k => $v) {
            if (false === array_key_exists($k, static::$aliases)) {
                static::$aliases[$k] = $localFile->getRealpath($v);
            }
        }
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

    /**
     * 判断给定选项值里是否设置某选项
     *
     * @param integer $flags 选项值
     * @param integer $flag 待判断的选项值
     *
     * @return boolean
     */
    public static function hasFlag($flags, $flag)
    {
        return $flags === ($flag | $flags);
    }

    /**
     * 创建一个对象
     *
     * @param array $params
     * - class：表示类名，可使用别名
     * - 其他：该类的属性，初始化这些属性
     * @param array $config
     * - 构造函数传参
     *
     * @return object
     */
    public static function createObject($params, $config = [])
    {
        if (is_array($params) && isset($params['class'])) {
            try {
                $class = $params['class'];
                unset($params['class']);
                $reflection = new ReflectionClass(self::getAlias($class));
                $object = $reflection->newInstanceArgs($config);
                foreach ($params as $name => $value) {
                    self::set($object, $name, $value);
                }
                return $object;
            } catch (Exception $e) {
                throw new Exception('初始化 ' . $class . ' 失败', $e->getCode(), $e);
            }
        }
        return null;
    }
}
