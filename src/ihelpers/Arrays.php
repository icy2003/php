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

/**
 * 数组类
 *
 * 常见数组格式的拼装和处理
 */
class Arrays
{
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
     * 选取数组中某几项字段
     *
     * @param array $array
     * @param array $keys 某几项字段，支持 I::get 的键格式
     *
     * @return array
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testColumns
     */
    public static function columns($array, $keys)
    {
        $result = [];
        foreach ($array as $field => $row) {
            foreach ($keys as $key) {
                $result[$field][$key] = I::get($row, $key);
            }
        }

        return array_filter($result);
    }

    /**
     * 返回数组中指定的一列
     *
     * - array_column 要求 PHP >= 5.5，这个是兼容 5.5 以下的
     * - 如果需要取某几项，使用 Arrays::columns
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
     * 检查数组里是否有指定的所有键名或索引
     *
     * - array_key_exists：检测一个指定的键
     * - Arrays::keyExistsOne：检测数组里是否存在指定的某些键
     *
     * @param array $keys 要检查的键
     * @param array $array
     * @param array $diff 引用返回不包含的键
     *
     * @return boolean
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testKeyExistsAll
     */
    public static function keyExistsAll($keys, $array, &$diff = null)
    {

        return I::isEmpty($diff = array_diff($keys, array_keys($array)));
    }

    /**
     * 检查数组里是否有指定的所有键名或索引
     *
     * @param array $keys 要检查的键
     * @param array $array
     * @param array $find 引用返回包含的键
     *
     * @return boolean
     */
    public static function keyExistsSome($keys, $array, &$find = null)
    {
        return !I::isEmpty($find = array_intersect($keys, array_keys($array)));
    }

    /**
     * 参照 PHP 的 array_combine 函数，array_combine 得到的是一行记录的格式，该函数得到多行
     *
     * @param array $keys 作为键的字段
     * @param array $arrays
     *
     * @return array
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testCombines
     */
    public static function combines($keys, $arrays)
    {
        $result = [];
        foreach ($arrays as $k => $array) {
            $result[$k] = self::combine($keys, $array);
        }

        return $result;
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
            return array_combine($keys, $values);
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
                throw new \LogicException('步长必须大于 0');
            }
            for ($i = $start; $i <= $end; $i += $step) {
                yield $i;
            }
        } elseif ($start > $end) {
            if ($step >= 0) {
                throw new \LogicException('步长必须小于 0');
            }
            for ($i = $start; $i >= $end; $i += $step) {
                yield $i;
            }
        } else {
            yield $start;
        }
    }

    /**
     * 矩阵转置
     *
     * @param array $array 待转置的矩阵
     *
     * @return array
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testTransposed
     */
    public static function transposed($array)
    {
        $data = [];
        foreach ($array as $r => $row) {
            foreach ($row as $c => $col) {
                $data[$c][$r] = $col;
            }
        }
        return $data;
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
     * 把数组里逗号字符串拆分，并且去掉重复的部分
     *
     * @param array $array
     *
     * @return array
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testToPart
     */
    public static function toPart($array)
    {
        return array_values(
            array_filter(
                array_keys(
                    array_flip(
                        explode(',', implode(',', $array))
                    )
                )
            )
        );
    }

    /**
     * 普通二维数组转化成 Excel 单元格二维数组
     *
     * @param array $array
     *
     * @return array
     *
     * @test icy2003\php_tests\ihelpers\ArraysTest::testToCellArray
     */
    public static function toCellArray($array)
    {
        $data = [];
        $rowIndex = 0;
        foreach ($array as $row) {
            $rowIndex++;
            $colIndex = 'A';
            foreach ($row as $col) {
                $data[$rowIndex][$colIndex++] = $col;
            }
        }
        return $data;
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
        if (0 === count($array)) {
            return null;
        }
        $p = $pos % count($array);
        if (0 === $p) {
            $p = count($array);
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
        if (0 === count($array)) {
            return null;
        }
        $p = $pos % count($array);
        if (0 === $p) {
            $p = count($array);
        }
        end($array);
        for ($i = 1; $i < $p; $i++) {
            prev($array);
        }
        return current($array);
    }

    /**
     * 用给定的值填充数组
     *
     * - array_fill：第一参数在为负的时候，生成的数组的第二个元素是从 0 开始的！
     *
     * @param int $startIndex 返回的数组的第一个索引值
     * @param int $num 插入元素的数量。如果为 0 或者负数，则返回空数组
     * @param mixed $value 用来填充的值
     *
     * @return array
     */
    public static function fill($startIndex, $num, $value)
    {
        if ($num <= 0) {
            return [];
        }
        $array = [];
        foreach (self::rangeGenerator($startIndex, $startIndex + $num - 1) as $key) {
            $array[$key] = $value;
        }
        return $array;
    }

    /**
     * 计算数组中的单元数目，或对象中的属性个数
     *
     * - count：在非数组情况下，除了 null 会返回 0，其他都返回 1，囧
     *
     * @param array $array 数组
     *
     * @return integer
     */
    public static function count($array)
    {
        if (is_array($array)) {
            return count($array);
        }
        return 0;
    }

    /**
     * 让 var_export 返回 `[]` 的格式
     *
     * @param mixed $expression 变量
     * @param bool $return 默认值 为 true，即返回字符串而不是输出
     *
     * @return mixed
     */
    public static function export($expression, $return = true)
    {
        $export = var_export($expression, true);
        $export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
        $array = preg_split("/\r\n|\n|\r/", $export);
        $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [null, ']$1', ' => ['], $array);
        $export = implode(PHP_EOL, array_filter(["["] + $array));
        if (true === $return) {
            return $export;
        } else {
            echo $export;
        }
    }

    /**
     * 返回矩阵的列数和行数
     *
     * - 返回两个元素的一维数组，第一个元素表示矩阵的列数，第二个元素表示矩阵的行数
     *
     * @param array $array
     *
     * @return array
     */
    public static function colRowCount($array)
    {
        return [self::count(self::first($array)), self::count($array)];
    }
}
