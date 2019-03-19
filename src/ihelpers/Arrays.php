<?php

namespace icy2003\php\ihelpers;

/**
 * 数组处理
 */
class Arrays
{
    /**
     * 以各个元素的某字段值作为键重新指回该元素，此值对于该元素需唯一
     *
     * @param array  $array
     * @param string $index 用来作为键的某字段
     *
     * @return array
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
     * 选取数组中某几项字段.
     *
     * @param array $array
     * @param array $fields 某几项字段
     * @param int $dimension 维度，默认 2 维数组
     *
     * @return array
     */
    public static function columns($array, $fields, $dimension = 2)
    {
        $result = [];
        if (2 === $dimension) {
            foreach ($array as $key => $row) {
                foreach ($fields as $field) {
                    if (array_key_exists($field, $row)) {
                        $result[$key][$field] = $row[$field];
                    }
                }
            }
        } elseif (1 === $dimension) {
            $result = array_intersect_key($array, array_flip($fields));
        }

        return $result;
    }

    /**
     * array_column 要求 PHP >= 5.5，这个是兼容 5.5 以下的
     * 如果需要取某几项，建议使用 Arrays::columns
     * @see http://php.net/array_column
     *
     * @param array $array
     * @param string $column
     * @param string $index
     * @return array
     */
    public static function arrayColumn($array, $column, $index = null)
    {
        if (function_exists('array_column')) {
            return array_column($array, $column, $index);
        } else {
            $result = [];
            foreach ($array as $row) {
                $data = !empty($row[$column]) ? $row[$column] : null;
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
     * 检测数组里是否有某些键.
     *
     * @param array $keys
     * @param array $array
     *
     * @return boolean
     */
    public static function arrayKeysExists($keys, $array)
    {

        return Env::isEmpty(array_diff($keys, array_keys($array)));
    }

    /**
     * 参照 PHP 的 array_combine 函数，array_combine 得到的是一行记录的格式，该函数得到多行
     * @see http://php.net/array_combine
     *
     * @param array $keys
     * @param array $arrays
     *
     * @return array
     */
    public static function arrayCombines($keys, $arrays)
    {
        $result = [];
        foreach ($arrays as $k => $array) {
            $result[$k] = array_combine($keys, $array);
        }

        return $result;
    }
    /**
     * 递归地合并多个数组，区别于 array_merge_recursive，如果有相同的键，后者会覆盖前者
     * @see http://php.net/array_merge_recursive
     *
     * @param array $a
     * @param array $b
     *
     * @return array
     */
    public static function arrayMergeRecursive($a, $b)
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
                    $res[$k] = self::arrayMergeRecursive($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    /**
     * 获取数组某个值，以点代替数组层级
     *
     * @param array $array
     * @param string $keyString
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public static function value($array, $keyString, $defaultValue = null)
    {
        return Env::value($array, $keyString, $defaultValue);
    }

    /**
     *  range 的优化版
     * @see http://php.net/manual/zh/language.generators.overview.php
     *
     * @param integer $start
     * @param integer $end
     * @param integer $step
     * @version PHP >= 5.5
     *
     * @return \Generator
     */
    public static function range($start, $end, $step = 1)
    {
        if ($start < $end) {
            if ($step <= 0) {
                throw new \LogicException("步长必须大于 0 ");
            }
            for ($i = $start; $i <= $end; $i += $step) {
                yield $i;
            }
        } elseif ($start > $end) {
            if ($step >= 0) {
                throw new \LogicException("步长必须小于 0");
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
     * @param array $array
     *
     * @return array
     */
    public static function arrayTransposed($array)
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
     * @param callback $callback
     *
     * @return mixed
     */
    public static function detect($array, $callback)
    {
        foreach ($array as $key => $item) {
            if (Env::trigger($callback, [$item, $key])) {
                return $item;
            }
        }
        return null;
    }

    /**
     * 找到符合条件的所有项
     *
     * @param array $array
     * @param callback $callback 条件回调
     * @param callback $filter 对符合条件的项进行回调处理并返回
     *
     * @return array
     */
    public static function all($array, $callback, $filter = null)
    {
        $all = [];
        foreach ($array as $key => $item) {
            if (Env::trigger($callback, [$item, $key])) {
                if (null !== $filter) {
                    $all[$key] = Env::trigger($filter, [$item, $key]);
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
     * 原生函数需要 PHP7.3.0+ 才能支持
     *
     * @param array $array
     *
     * @return string
     */
    public static function arrayKeyLast($array)
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
     * 原生函数需要 PHP7.3.0+ 才能支持
     *
     * @param array $array
     *
     * @return string
     */
    public static function arrayKeyFirst($array)
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
     */
    public static function toPart($array)
    {
        return array_filter(array_keys(array_flip(explode(',', implode(',', $array)))));
    }

    /**
     * 普通二维数组转化成 Excel 单元格二维数组
     *
     * @param array $array
     *
     * @return array
     */
    public static function toCellArray($array)
    {
        $data = [];
        $rowIndex = 0;
        foreach ($array as $row) {
            $rowIndex++;
            $colIndex = "A";
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
     * ps：索引数组必须是下标从 0 开始的数组，键是数字还是字符串类型的数字无所谓
     *
     * @param array $array
     *
     * @return boolean
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
     * 返回数组的顺数第 n 个元素，其中 n >= 1 且为整数
     *
     * ps：支持关联数组，超过数组长度会对数组长度求余后查找
     *
     * @param array $array
     * @param int $pos 顺数第 n 个，默认 1
     *
     * @return mixed
     */
    public static function first($array, $pos = 1)
    {
        $p = $pos % count($array);
        for ($i = 1; $i < $p; $i++) {
            next($array);
        }
        return current($array);
    }

    /**
     * 返回数组的倒数第 n 个元素，其中 n >= 1 且为整数
     *
     * ps：支持关联数组，超过数组长度会对数组长度求余后查找
     *
     * @param array $array
     * @param int $pos 倒数第 n 个，默认 1
     *
     * @return mixed
     */
    public static function last($array, $pos = 1)
    {
        $p = $pos % count($array);
        end($array);
        for ($i = 1; $i < $p; $i++) {
            prev($array);
        }
        return current($array);
    }
}
