<?php

namespace icy2003\ihelpers;

/**
 * 针对一些特殊数据结构的数组操作类.
 * 该类多用于简化对数据库数据集的操作，如没有特殊说明，数组多为查询出来的二维键值对数组，，“元素”指一行数据，“字段”指字段
 * 示例数组：
 * $array = [
 *      ['id'=>'a', 'name'=>'aa', 'value'=>'aaa'],
 *      ['id'=>'b', 'name'=>'bb', 'value'=>'bbb']
 * ];.
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
     *
     * @example
     * ```php
     * $result = Arrays::indexBy($array, 'id');
     * ```
     * $result
     * ```php
     * [
     *      'a'=>['id'=>'a', 'name'=>'aa', 'value'=>'aaa'],
     *      'b'=>['id'=>'b', 'name'=>'bb', 'value'=>'bbb']
     * ]
     * ```
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
     *
     * @example
     * ```php
     * $result = Arrays::columns($array, ['name','value'], 2);
     * ```
     * $result
     * ```php
     * [
     *      ['name'=>'aa','value'=>'aaa'],
     *      ['name'=>'bb','value'=>'bbb'],
     * ]
     * ```
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
                if (null === $inedx) {
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
     *
     * @example
     * ```php
     * $result1 = Arrays::arrayKeysExists(['id','name'], $array);
     * $result2 = Arrays::arrayKeysExists(['id','id2'], $array);
     * ```
     * $result1,$result2:
     * ```php
     * true
     * false
     * ```
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
     *
     * @example
     * ```php
     * $arrays = [['a1','a2','a3'],['b1','b2','b3']];
     * $result = Arrays::arrayCombines(['name1','name2','name3'], $arrays);
     * ```
     * $result
     * ```php
     * [
     *      ['name1'=>'a1', 'name2'=>'a2', 'name3'=>'a3'],
     *      ['name1'=>'b1', 'name2'=>'b2', 'name3'=>'b3'],
     * ]
     * ```
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
        } else if ($start > $end) {
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
}
