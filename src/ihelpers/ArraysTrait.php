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
 * Arrays 扩展函数
 */
trait ArraysTrait
{

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
     * @test icy2003\php_tests\ihelpers\ArraysTest::testKeyExistsAll
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
     * 将 CSV 文本转成数组
     *
     * @param string $csvString
     *
     * @return array
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
     * - 第一参数如果是布尔值，则此方法等同于 array_search
     * - 第一参数如果是回调函数，则找到的条件为：回调值为 true
     * - 第三参数如果是 false，则回调值只需要 true 值即可（例如：1）
     *
     * @param mixed|callback $search 搜索的值
     * @param array $array 这个数组
     * @param boolean $isStrict 是否检查完全相同的元素
     *
     * @return mixed
     */
    public static function search($search, $array, $isStrict = false)
    {
        if (false === is_callable($search)) {
            return array_search($search, $array, $isStrict);
        }
        foreach ($array as $key => $row) {
            $result = I::trigger($search, [$row]);
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
     */
    public function increment(&$array, $key, $step = 1)
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
     */
    public function decrement($array, $key, $step = 1)
    {
        $array[$key] = I::get($array, $key, 0) - $step;
        return $array[$key];
    }
}
