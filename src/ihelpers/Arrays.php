<?php
/**
 * Class Arrays
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

use icy2003\php\C;
use icy2003\php\I;

/**
 * 数组类
 *
 * 常见数组格式的拼装和处理
 *
 * @test icy2003\php_tests\ihelpers\ArraysTest
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
     * @tested
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
     * @tested
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
     * - USE_CUSTOM
     *
     * @see http://php.net/array_column
     *
     * @param array $array
     * @param string $column 需要被取出来的字段
     * @param string $index 作为 index 的字段
     *
     * @return array
     *
     * @tested
     */
    public static function column($array, $column, $index = null)
    {
        if (function_exists('array_column') && false === I::ini('USE_CUSTOM')) {
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
     *
     * @tested
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
     * @tested
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
     *
     * @tested
     */
    public static function rangeGenerator($start, $end, $step = 1)
    {
        if ($start < $end) {
            C::assertTrue($step > 0, '步长必须大于 0');
            for ($i = $start; $i <= $end; $i += $step) {
                yield $i;
            }
        } elseif ($start > $end) {
            C::assertTrue($step < 0, '步长必须小于 0');
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
     * @tested
     */
    public static function detectFirst($array, $callback)
    {
        foreach ($array as $key => $item) {
            if (true === I::call($callback, [$item, $key])) {
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
     * @tested
     */
    public static function detectAll($array, $callback, $filter = null)
    {
        $all = [];
        foreach ($array as $key => $item) {
            if (true === I::call($callback, [$item, $key])) {
                if (null !== $filter) {
                    $all[$key] = I::call($filter, [$item, $key]);
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
     * - USE_CUSTOM
     *
     * @param array $array
     *
     * @return string|null
     *
     * @tested
     */
    public static function keyLast($array)
    {
        if (!is_array($array) || empty($array)) {
            return null;
        }
        if (function_exists('array_key_last') && false === I::ini('USE_CUSTOM')) {
            return array_key_last($array);
        }
        end($array);
        return key($array);
    }

    /**
     * 返回数组的第一个元素的键
     *
     * - array_key_first：需要 PHP7.3.0+ 才能支持
     * - USE_CUSTOM
     *
     * @param array $array
     *
     * @return string|null
     *
     * @tested
     */
    public static function keyFirst($array)
    {
        if (!is_array($array) || empty($array)) {
            return null;
        }
        if (function_exists('array_key_first') && false === I::ini('USE_CUSTOM')) {
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
     * @return integer
     *
     * @tested
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
     * @tested
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
     * @tested
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
     * @tested
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
     * @tested
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
     * @param callback|mixed $callback 回调，返回回调值为 true 的项，如果此参数是非回调类型，表示查询和此值严格相等的项
     * @param boolean $isStrict 是否为严格模式，如果为 false，回调值为 true 值的也会返回，为字符串时不使用严格比较
     *
     * @return integer
     *
     * @tested
     */
    public static function count($array, $callback = null, $isStrict = true)
    {
        $count = 0;
        if (is_array($array)) {
            if (null === $callback) {
                return count($array);
            } else {
                $function = $callback;
                if (false === is_callable($callback)) {
                    $function = function ($row) use ($callback, $isStrict) {
                        return true === $isStrict ? $row === $callback : $row == $callback;
                    };
                }
                foreach ($array as $key => $row) {
                    if (true === I::call($function, [$row, $key])) {
                        $count++;
                    }
                }

            }
        }
        return $count;
    }

    /**
     * 返回指定长度的数组，不足的值设置为 null
     *
     * @param array $array
     * @param integer $count 指定长度
     * @param callback $callback 回调参数：数组的值、数组的键
     *
     * @return array
     *
     * @tested
     */
    public static function lists($array, $count = null, $callback = null)
    {
        null === $count && $count = self::count($array);
        $arrayCount = self::count($array);
        if ($arrayCount >= $count) {
            $return = $array;
        } else {
            $return = self::merge($array, self::fill(0, $count - $arrayCount, null));
        }
        if (null !== $callback) {
            foreach ($return as $key => $value) {
                $return[$key] = I::call($callback, [$value, $key]);
            }
        }
        return $return;
    }

    /**
     * 获取指定某些键的项的值
     *
     * @param array $array
     * @param array|string $keys 数组或逗号字符串
     *
     * @return array
     *
     * @tested
     */
    public static function values($array, $keys = null)
    {
        return array_values(self::some($array, $keys));
    }

    /**
     * 获取指定某些键的项
     *
     * @param array $array
     * @param array|string $keys 数组或都好字符串
     *
     * @return array
     *
     * @tested
     */
    public static function some($array, $keys = null)
    {
        if (null === $keys) {
            return $array;
        }
        $keys = Strings::toArray($keys);
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * 获取指定除了某些键的项
     *
     * @param array $array
     * @param array|string $keys
     *
     * @return array
     *
     * @tested
     */
    public static function exceptedKeys($array, $keys)
    {
        $keys = Strings::toArray($keys);
        return array_diff_key($array, array_flip($keys));
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
     * @tested
     */
    public static function keyExistsAll($keys, $array, &$diff = null)
    {

        return I::isEmpty($diff = array_diff($keys, array_keys($array)));
    }

    /**
     * 检查数组里是否有指定的某些键名或索引
     *
     * @param array $keys 要检查的键
     * @param array $array
     * @param array $find 引用返回包含的键
     *
     * @return boolean
     *
     * @tested
     */
    public static function keyExistsSome($keys, $array, &$find = null)
    {
        return !I::isEmpty($find = array_intersect($keys, array_keys($array)));
    }

    /**
     * 检查数组里是否有指定的所有值
     *
     * @param array $values 要检查的值
     * @param array $array
     * @param array $diff 引用返回不包含的值
     *
     * @return boolean
     *
     * @tested
     */
    public static function valueExistsAll($values, $array, &$diff = null)
    {
        return I::isEmpty($diff = array_diff($values, array_values($array)));
    }

    /**
     * 检查数组里是否有指定的某些值
     *
     * @param array $values 要检查的值
     * @param array $array
     * @param array $find 引用返回包含的值
     *
     * @return boolean
     *
     * @tested
     */
    public static function valueExistsSome($values, $array, &$find = null)
    {
        return !I::isEmpty($find = array_intersect($values, array_values($array)));
    }

    /**
     * 参照 PHP 的 array_combine 函数，array_combine 得到的是一行记录的格式，该函数得到多行
     *
     * - arrays 里的每个数组会和 keys 使用 self::combine 合并，最终合并成为一个二维数组
     *
     * @param array $keys 作为键的字段
     * @param array $arrays
     *
     * @return array
     *
     * @tested
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
     * 把数组里逗号字符串拆分，并且去掉重复的部分
     *
     * @param array $array
     *
     * @return array
     *
     * @tested
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
     * 矩阵转置
     *
     * @param array $array 待转置的矩阵
     *
     * @return array
     *
     * @tested
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
     * 普通二维数组转化成 Excel 单元格二维数组
     *
     * @param array $array
     *
     * @return array
     *
     * @tested
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
     * 返回矩阵的列数和行数
     *
     * - 返回两个元素的一维数组，第一个元素表示矩阵的列数，第二个元素表示矩阵的行数
     *
     * @param array $array
     *
     * @return array
     *
     * @tested
     */
    public static function colRowCount($array)
    {
        return [self::count(self::first($array)), self::count($array)];
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
     *
     * @tested
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
     * 让 var_export 返回 `[]` 的格式
     *
     * @param mixed $expression 变量
     * @param bool $return 默认值 为 true，即返回字符串而不是输出
     *
     * @return mixed
     *
     * @tested
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
     * 将 CSV 文本转成数组
     *
     * @param string $csvString
     *
     * @return array
     *
     * @tested
     */
    public static function fromCsv($csvString)
    {
        $lines = explode(PHP_EOL, $csvString);
        $array = [];
        foreach ($lines as $line) {
            $array[] = explode(',', $line);
        }
        return $array;
    }

    /**
     * 在数组中搜索给定的值，如果成功则返回首个相应的键名
     *
     * - 第一参数如果不是回调函数，则此方法等同于 array_search
     * - 第一参数如果是回调函数，则找到的条件为：回调值为 true
     * - 第三参数如果是 false，则回调值只需要 true 值即可（例如：1）
     *
     * @param mixed|callback $search 搜索的值
     * @param array $array 这个数组
     * @param boolean $isStrict 是否检查完全相同的元素
     *
     * @return mixed|false
     *
     * @tested
     */
    public static function search($search, $array, $isStrict = false)
    {
        if (false === is_callable($search)) {
            return array_search($search, $array, $isStrict);
        }
        foreach ($array as $key => $row) {
            $result = I::call($search, [$row]);
            if (true === $isStrict && true === $result || false === $isStrict && true == $result) {
                return $key;
            }
        }
        return false;
    }

    /**
     * 递增数组的一个值并返回
     *
     * - 如果该值不存在，则默认为 0
     *
     * @param array $array 引用返回数组
     * @param string $key
     * @param integer $step 步长，默认 1
     *
     * @return double|integer
     *
     * @tested
     */
    public static function increment(&$array, $key, $step = 1)
    {
        $array[$key] = I::get($array, $key, 0) + $step;
        return $array[$key];
    }

    /**
     * 递减数组的一个值并返回
     *
     * - 如果该值不存在，则默认为 0
     *
     * @param array $array 引用返回数组
     * @param string $key
     * @param integer $step 步长，默认 1
     *
     * @return double|integer
     *
     * @tested
     */
    public static function decrement(&$array, $key, $step = 1)
    {
        $array[$key] = I::get($array, $key, 0) - $step;
        return $array[$key];
    }

    /**
     * in_array 的扩展
     *
     * @param mixed $value
     * @param array $array
     * @param boolean $isStrict 是否严格匹配，默认 false，即不严格
     * @param boolean $ignoreCase 是否忽略大小写，默认 false，不忽略
     *
     * @return boolean
     */
    public static function in($value, $array, $isStrict = false, $ignoreCase = false)
    {
        if (false === is_array($array)) {
            return false;
        }
        if (false === $ignoreCase) {
            return in_array($value, $array, $isStrict);
        } else {
            $value = Json::decode(strtolower(Json::encode($value)));
            $array = Json::decode(strtolower(Json::encode($array)));
            return in_array($value, $array, $isStrict);
        }
    }

}
