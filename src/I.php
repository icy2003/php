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
     * 支持类型：数组对象和 null、数字和字符串、布尔值、回调函数，依据数据类型有不同的含义（但是都很合理）
     *
     * @param mixed $mixed 混合类型
     *      - 当 $mixed 为**数组或对象**时，此方法用于按照层级获取值，用法如下：
     *          1. 对于一个多维数组 $array 来说，a.b.cd_ef 会拿 $array['a']['b']['cd_ef'] 的值
     *          2. 如果 $array['a'] 是对象，则先检查 getB 方法，然后检查 b 属性
     *          3. 如果 $array['a']['b'] 是对象，则检查 getCdEf 方法，然后检查 cd_ef 属性
     *      - 当 $mixed 为**布尔值**（即表达式）时，等价于三元操作符，例如 I::get(1 > 2, '真', '假')
     *      - 当 $mixed 为**字符串**时，等价于 Strings::sub，截取字符串
     *      - 当 $mixed 为 **null** 时，含义可被描述为：在使用 I::get($array, 'a.b', 1)，$array 意外的是 null，返回 1 是理所当然的
     *      - 当 $mixed 为**回调函数**，$mixed 的执行结果将作为 I::get 的返回值
     * @param mixed $keyString 取决于 $mixed 的类型：
     *      - 当 $mixed 为**数组或对象**时，$keyString 表示：点（.）分割代表层级的字符串，下划线用于对象中转化成驼峰方法，支持数组和对象嵌套
     *      - 当 $mixed 为**布尔值**（即表达式）时，$keyString 表示：$mixed 为 true 时返回的值
     *      - 当 $mixed 为**字符串**时，$keyString 强制转为整型，表示：截取 $mixed 时，子串的起始位置
     *      - 当 $mixed 为 **null** 时，此参数无效
     *      - 当 $mixed 为**回调函数**，如果 $mixed 的返回值代表 true（如：1），则执行此回调
     * @param mixed $defaultValue 取决于 $mixed 的类型：
     *      - 当 $mixed 为**数组或对象**时，$defaultValue 表示：拿不到值时会直接返回该默认值
     *      - 当 $mixed 为**布尔值**（即表达式）时，$defaultValue 表示：$mixed 为 false 时返回的值
     *      - 当 $mixed 为**字符串**时，$defaultValue 表示：截取 $mixed 时，子串的长度，null 时表示长度为 1
     *      - 当 $mixed 为 **null** 时，返回 $defaultValue
     *      - 当 $mixed 为**回调函数**，如果 $mixed 的返回值代表 false（如：0），则执行此回调
     *
     * @return mixed
     */
    public static function get($mixed, $keyString, $defaultValue = null)
    {
        if (is_bool($mixed)) { // 布尔类型
            return true === $mixed ? $keyString : $defaultValue;
        } elseif (is_callable($mixed)) { // 回调
            $result = self::call($mixed);
            if ($result) {
                self::call($keyString);
            } else {
                self::call($defaultValue);
            }
            return $result;
        } elseif (is_array($mixed) || is_object($mixed)) { // 数组和对象
            if (false === is_string($keyString) && is_callable($keyString)) {
                $mixed = self::call($keyString, [$mixed]);
            } else {
                // 如果是个常规数组，直接取值，只支持一维数组
                if (is_array($mixed) && array_key_exists($keyString, $mixed)) {
                    return $mixed[$keyString];
                }
                $keyArray = explode('.', $keyString);
                foreach ($keyArray as $key) {
                    if (is_array($mixed)) {
                        if (array_key_exists($key, $mixed) && null !== $mixed[$key]) {
                            $mixed = $mixed[$key];
                        } else {
                            return $defaultValue;
                        }
                    } elseif (is_object($mixed)) {
                        $method = 'get' . ucfirst(Strings::toCamel($key));
                        if (method_exists($mixed, $method)) {
                            $mixed = $mixed->$method();
                        } elseif (property_exists($mixed, $key) && null !== $mixed->$key) {
                            $mixed = $mixed->$key;
                        } else {
                            try {
                                $mixed = $mixed->$key;
                                if (null === $mixed) {
                                    return $defaultValue;
                                }
                            } catch (Exception $e) {
                                return $defaultValue;
                            }

                        }
                    } else {
                        return self::get($mixed, $key, $defaultValue);
                    }
                }
            }
            return $mixed;
        } elseif (is_string($mixed) || is_numeric($mixed)) { // 字符串或数字
            $pos = (int) $keyString;
            $length = null === $defaultValue ? 1 : (int) $defaultValue;
            return Strings::sub($mixed, $pos, $length);
        } elseif (null === $mixed) { // null
            return $defaultValue;
        } else { // 资源
            return $defaultValue;
        }
    }

    /**
     * 设置值
     *
     * @param array|object $mixed 对象或数组
     * @param string $key 键
     * @param mixed $value 值
     * @param boolean $overWrite 如果对应的值存在，是否用给定的值覆盖，默认 true，即：是
     *
     * @return mixed
     */
    public static function set(&$mixed, $key, $value, $overWrite = true)
    {
        $get = self::get($mixed, $key);
        if (null === $get || true === $overWrite) {
            if (is_array($mixed)) {
                $mixed[$key] = $value;
            } elseif (is_object($mixed)) {
                $method = 'set' . ucfirst(Strings::toCamel($key));
                if (method_exists($mixed, $method)) {
                    $mixed->$method($value);
                } elseif (property_exists($mixed, $key)) {
                    $mixed->$key = $value;
                } else {
                    throw new Exception('无法设置值');
                }
            }
            return $value;
        }
        return $get;
    }

    /**
     * 触发回调
     *
     * @param callback $callback 回调函数
     * @param array $params 回调参数
     * @return mixed
     */
    public static function call($callback, $params = [])
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
        return false !== ($ini = ini_get($key)) ? $ini : (false !== ($ini = get_cfg_var($key)) ? $ini : $default);
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
        '@vendor' => __DIR__ . '/../../../../vendor',
        '@icy2003/php_tests' => __DIR__ . '/../tests',
        '@icy2003/php_runtime' => __DIR__ . '/../runtime',
        '@icy2003/php' => __DIR__,
    ];

    /**
     * 用别名获取真实路径
     *
     * @param string $alias 别名
     *
     * @return string|boolean
     */
    public static function getAlias($alias)
    {
        $alias = Strings::replace($alias, ["\\" => '/']);
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
        if ($result = self::call(['\Yii', 'getAlias'], [$alias])) {
            self::setAlias($alias, $result);
            return $result;
        }

        return false;
    }

    /**
     * 是否是 Yii2 项目
     *
     * @return boolean
     */
    public static function isYii2()
    {
        return method_exists('\Yii', 'getVersion');
    }

    /**
     * 设置别名
     *
     * @param string $alias 别名
     * @param string|null $path 路径
     *
     * @return void
     */
    public static function setAlias($alias, $path)
    {
        // 对 Yii2 的支持
        try {
            self::call(['\Yii', 'getAlias'], [$alias]);
        } catch (Exception $e) {
            self::call(['\Yii', 'setAlias'], [$alias, $path]);
        }
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
     * @param array|string $params
     *      - 字符串：该字符串将被作为类名转成数组处理
     *      - 数组：
     *          1. class：表示类名
     *          2. 其他：该类的属性，初始化这些属性或者调用相应的 set 方法
     * @param array $config
     * - 构造函数传参
     *
     * @return object
     * @throws Exception
     */
    public static function obj($params, $config = [])
    {
        if (is_string($params)) {
            $params = ['class' => $params];
        }
        if (is_array($params) && isset($params['class'])) {
            try {
                $class = $params['class'];
                unset($params['class']);
                $reflection = new ReflectionClass($class);
                $object = $reflection->newInstanceArgs($config);
                foreach ($params as $name => $value) {
                    self::set($object, $name, $value);
                }
                return $object;
            } catch (Exception $e) {
                throw new Exception('初始化 ' . $class . ' 失败', $e->getCode(), $e);
            }
        }
        throw new Exception('必须带 class 键来指定一个类');
    }

    /**
     * 静态配置
     *
     * @var array
     */
    public static $ini = [
        'USE_CUSTOM' => false,
        'EXT_LOADED' => true,
    ];

    /**
     * 读取或设置一个全局配置
     *
     * - 该配置是利用静态类进行存储的
     * - 如果给定 $value，则为设置，不给则为获取
     * - 可选默认配置有：
     *      1. USE_CUSTOM：默认 false，即尝试使用 php 原生函数的实现，如果此参数为 true，则使用 icy2003/php 的实现
     *      2. EXT_LOADED：默认 true，即尝试检测是否有扩展，如果为 false，直接认为没有该扩展
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void|mixed
     */
    public static function ini($key, $value = null)
    {
        if (null !== $value) {
            self::$ini[$key] = $value;
        } else {
            return self::get(self::$ini, $key);
        }
    }

    /**
     * 是否有加载 PHP 扩展
     *
     * @param string $extName
     *
     * @return boolean
     */
    public static function isExt($extName)
    {
        if (false === extension_loaded($extName) || false === self::ini('EXT_LOADED')) {
            return false;
        }
        return true;
    }

    /**
     * 判断当前操作系统是不是 windows
     *
     * @return boolean
     */
    public static function isWin()
    {
        return 'WIN' === strtoupper(substr(PHP_OS, 0, 3));
    }
}
