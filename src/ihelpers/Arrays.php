<?php
/**
 * Class Arrays
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

use icy2003\php\I;
use LogicException;

/**
 * 数组类
 *
 * 常见数组格式的拼装和处理
 */
class Arrays
{

    use ArraysTrait;

    /**
     * 以各个元素的某字段值作为键重新指回该元素，此值对于该元素需唯一
     *
     * @param array  $array
     * @param string $index 用来作为键的某字段
     * @param boolean $isMerge 是否合并相同键的项到数组，默认否（也就是后者覆盖前者）
     *
     * @return array
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testIndexBy
     */
    public static function indexBy($array, $index, $isMerge = false)
    {
        $result = [];
        foreach ($array as $row) {
            if (!array_key_exists($index, $row)) {
                return [];
            }
            if (false === $isMerge) {
                $result[$row[$index]] = $row;
            } else {
                $result[$row[$index]][] = $row;
            }
        }

        return $result;
    }

    /**
     * 选取数组中指定键的某几列
     *
     * - 简单理解就是：从数据库里查出来几条数据，只拿其中的几个属性
     * - 当 $dimension 为 2 时，理解为从几条数据里拿属性
     * - 当 $dimension 为 1 时，理解为从一条数据里拿属性
     *
     * @param array $array
     * @param array $keys 某几项字段，支持 I::get 的键格式，如果给 null，则返回原数组
     * @param integer $dimension 维度，只能为 1 或 2，默认 2，表示处理二维数组
     *
     * @return array
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testColumns
     */
    public static function columns($array, $keys = null, $dimension = 2)
    {
        if (null === $keys) {
            return $array;
        }
        $result = [];
        if (2 === $dimension) {
            foreach ($array as $k => $row) {
                foreach ($keys as $key) {
                    if (array_key_exists($key, $row)) {
                        $result[$k][$key] = I::get($row, $key);
                    } else {
                        $result[$k][$key] = null;
                    }
                }
            }
        }
        if (1 === $dimension) {
            foreach ($array as $k => $row) {
                if (in_array($k, $keys, true)) {
                    $result[$k] = $row;
                } else {
                    $result[$k] = null;
                }
            }
        }

        return $result;
    }

    /**
     * 返回二维（或者更高）数组中指定键的一列的所有值
     *
     * - array_column 要求 PHP >= 5.5，这个是兼容 5.5 以下的
     * - 如果需要取某几项，使用 Arrays::columns
     * - 简单理解就是：从数据库里查出来几条数据，只要其中某个属性的所有值
     *
     * @see http://php.net/array_column
     *
     * @param array $array
     * @param string $column 需要被取出来的字段
     * @param string $index 作为 index 的字段
     *
     * @return array
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testColumn
     */
    public static function column($array, $column, $index = null)
    {
        if (function_exists('array_column')) {
            return array_column($array, $column, $index);
        } else {
            $result = [];
            foreach ($array as $row) {
                $data = I::get($row, $column);
                if (null === $index) {
                    $result[] = $data;
                } else {
                    $result[$row[$index]] = $data;
                }
            }

            return $result;
        }
    }

    /**
     * 创建一个数组，用一个数组的值作为其键名，另一个数组的值作为其值
     *
     * - array_combine：两个数组元素个数不一致将报错
     * - 在两个数组元素个数不一致时，以键为准:
     *      1.键比值多，值都被填充为 null
     *      2.值比键多，值被舍去
     * ```
     *
     * @see http://php.net/array_combine
     *
     * @param array $keys
     * @param array $values
     *
     * @return array
     */
    public static function combine($keys, $values)
    {
        if (count($keys) == count($values)) {
            return (array) array_combine($keys, $values);
        }
        $array = [];
        foreach ($keys as $index => $key) {
            $array[$key] = I::get($values, $index);
        }
        return $array;
    }

    /**
     * 递归地合并多个数组
     *
     * - array_merge_recursive：如果有相同的键，后者会覆盖前者
     * - 此函数会合并两个相同键的值到一个数组里
     *
     * @see http://php.net/array_merge_recursive
     *
     * @param array $a 数组1
     * @param array $b 数组2（可以任意个数组）
     *
     * @return array
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testMerge
     */
    public static function merge($a, $b)
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            foreach (array_shift($args) as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $res)) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::merge($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    /**
     *  range 的性能优化版
     *
     * @see http://php.net/manual/zh/language.generators.overview.php
     * @version PHP >= 5.5
     *
     * @param integer $start 开始
     * @param integer $end 结束
     * @param integer $step 步长
     *
     * @return \Generator
     * @throws \LogicException
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testRangeGenerator
     */
    public static function rangeGenerator($start, $end, $step = 1)
    {
        if ($start < $end) {
            if ($step <= 0) {
                throw new LogicException('步长必须大于 0');
            }
            for ($i = $start; $i <= $end; $i += $step) {
                yield $i;
            }
        } elseif ($start > $end) {
            if ($step >= 0) {
                throw new LogicException('步长必须小于 0');
            }
            for ($i = $start; $i >= $end; $i += $step) {
                yield $i;
            }
        } else {
            yield $start;
        }
    }

    /**
     * 找到符合条件的第一项
     *
     * @param array $array
     * @param callback $callback 条件回调，结果为 true 的第一项会被取出
     *
     * @return mixed
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testDetectFirst
     */
    public static function detectFirst($array, $callback)
    {
        foreach ($array as $key => $item) {
            if (true === I::trigger($callback, [$item, $key])) {
                return $item;
            }
        }
        return null;
    }

    /**
     * 找到符合条件的所有项
     *
     * @param array $array
     * @param callback $callback 条件回调，结果为 true 的所有项会被取出
     * @param callback $filter 对符合条件的项进行回调处理并返回
     *
     * @return array
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testDetectAll
     */
    public static function detectAll($array, $callback, $filter = null)
    {
        $all = [];
        foreach ($array as $key => $item) {
            if (true === I::trigger($callback, [$item, $key])) {
                if (null !== $filter) {
                    $all[$key] = I::trigger($filter, [$item, $key]);
                } else {
                    $all[$key] = $item;
                }
            }
        }
        return $all;
    }

    /**
     * 返回数组的最后一个元素的键
     *
     * - array_key_last：需要 PHP7.3.0+ 才能支持
     *
     * @param array $array
     *
     * @return string
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testKeyLast
     */
    public static function keyLast($array)
    {
        if (!is_array($array) || empty($array)) {
            return null;
        }
        if (function_exists('array_key_last')) {
            return array_key_last($array);
        }
        end($array);
        return key($array);
    }

    /**
     * 返回数组的第一个元素的键
     *
     * - array_key_first：需要 PHP7.3.0+ 才能支持
     *
     * @param array $array
     *
     * @return string
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testKeyFirst
     */
    public static function keyFirst($array)
    {
        if (!is_array($array) || empty($array)) {
            return null;
        }
        if (function_exists('array_key_first')) {
            return array_key_first($array);
        }
        reset($array);
        return key($array);
    }

    /**
     * 获取数组的维度
     *
     * @param array $array 多维数组
     *
     * @return int
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testDimension
     */
    public static function dimension($array)
    {
        if (!is_array($array)) {
            return 0;
        }
        $max = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $d = self::dimension($value) + 1;
                if ($d > $max) {
                    $max = $d;
                }
            }
        }
        return $max;
    }

    /**
     * 判断数组是不是关联数组
     *
     * @param array $array
     *
     * @return boolean
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testIsAssoc
     */
    public static function isAssoc($array)
    {
        if (is_array($array)) {
            $keys = array_keys($array);
            return $keys !== array_keys($keys);
        }
        return false;
    }

    /**
     * 判断数组是不是索引数组
     *
     * 索引数组必须是下标从 0 开始的数组，键是数字还是字符串类型的数字无所谓
     *
     * @param array $array
     *
     * @return boolean
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testIsIndexed
     */
    public static function isIndexed($array)
    {
        if (is_array($array)) {
            $keys = array_keys($array);
            return $keys === array_keys($keys);
        }
        return false;
    }

    /**
     * 返回数组的顺数第 n 个元素，其中 n >= 1 且为整数，空数组直接返回 null
     *
     * - 支持关联数组，超过数组长度会对数组长度求余后查找
     *
     * @param array $array
     * @param int $pos 顺数第 n 个，默认 1
     *
     * @return mixed
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testFirst
     */
    public static function first($array, $pos = 1)
    {
        if (0 === ($count = self::count($array))) {
            return null;
        }
        $p = $pos % $count;
        if (0 === $p) {
            $p = $count;
        }
        for ($i = 1; $i < $p; $i++) {
            next($array);
        }
        return current($array);
    }

    /**
     * 返回数组的倒数第 n 个元素，其中 n >= 1 且为整数，空数组直接返回 null
     *
     * - 支持关联数组，超过数组长度会对数组长度求余后查找
     *
     * @param array $array
     * @param int $pos 倒数第 n 个，默认 1
     *
     * @return mixed
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testLast
     */
    public static function last($array, $pos = 1)
    {
        if (0 === ($count = self::count($array))) {
            return null;
        }
        $p = $pos % $count;
        if (0 === $p) {
            $p = $count;
        }
        end($array);
        for ($i = 1; $i < $p; $i++) {
            prev($array);
        }
        return current($array);
    }

    /**
     * 计算数组中的单元数目
     *
     * - count：在非数组情况下，除了 null 会返回 0，其他都返回 1，囧
     * - $callback 参数用于对符合条件的项做筛选
     *
     * @param array $array 数组
     * @param callback|string $callback 回调，返回回调值为 true 的项，如果此参数是字符串，表示查询和此字符串严格相等的项
     * @param boolean $isStrict 是否为严格模式，如果为 false，回调值为 true 值的也会返回，为字符串时不使用严格比较
     *
     * @return integer
     */
    public static function count($array, $callback = null, $isStrict = true)
    {
        $count = 0;
        if (is_array($array)) {
            if (null === $callback) {
                return count($array);
            } elseif (is_string($callback) || is_callable($callback)) {
                $function = $callback;
                if (is_string($callback)) {
                    $function = function ($row) use ($callback, $isStrict) {
                        return true === $isStrict ? $row === $callback : $row == $callback;
                    };
                }
                foreach ($array as $key => $row) {
                    if (true === I::trigger($function, [$row, $key])) {
                        $count++;
                    }
                }

            }
        }
        return $count;
    }

}
