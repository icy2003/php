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
    public static function indexBy($array, $index)
    {
        $result = [];
        foreach ($array as $row) {
            if (empty($row[$index])) {
                return [];
            }
            $result[$row[$index]] = $row;
        }

        return $result;
    }

    /**
     * 选取数组中某几项字段.
     *
     * @param array $array
     * @param array $fields 某几项字段
     *
     * @return array
     *
     * @example
     * ```php
     * $result = Arrays::columns($array,['name','value']);
     * ```
     * $result
     * ```php
     * [
     *      ['name'=>'aa','value'=>'aaa'],
     *      ['name'=>'bb','value'=>'bbb'],
     * ]
     * ```
     */
    public static function columns($array, $fields)
    {
        $result = [];
        foreach ($array as $key => $row) {
            foreach ($fields as $field) {
                if (!empty($row[$field])) {
                    $result[$key][$field] = $row[$field];
                }
            }
        }

        return $result;
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
        $count = count($keys);
        $diff = array_diff($keys, array_keys($array));

        return count($diff) === $count;
    }

    /**
     * 参照 PHP 的 array_combine 函数，array_combine 得到的是一行记录的格式，该函数得到多行.
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
}
