<?php
/**
 * Trait MathAndTrigonometry-M
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

use icy2003\php\ihelpers\Arrays;

/**
 * MathAndTrigonometry-M
 */
trait MMAT
{
    /**
     * 返回一个数组的矩阵行列式的值
     *
     * - det(A) = ai1Ai1 + ... + ainAin
     *
     * @param array $array 必需。 行数和列数相等的数值数组
     *
     * @return double
     */
    public static function mdeterm($array)
    {
        $n = count($array);
        $sum = 0;
        $prod = 1;
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $x = ($j + $i) % $n;
                $y = $j % $n;
                $prod *= $array[$x][$y];
            }
            $sum += $prod;
            $prod = 1;
            for ($j = $n - 1; $j >= 0; $j--) {
                $x = ($j + $i) % $n;
                $y = ($n - $j) % $n;
                $prod *= $array[$x][$y];
            }
            $sum -= $prod;
            $prod = 1;
        }
        return $sum;
    }

    /**
     * 返回数组中存储的矩阵的逆矩阵
     *
     * @todo
     *
     * @return array
     */
    public static function minverse()
    {
        return [];
    }

    /**
     * 返回两个数组的矩阵乘积。 结果矩阵的行数与 array1 的行数相同，矩阵的列数与 array2 的列数相同
     *
     * - Aij = ∑(k = 1, n)Bik * Ckj，其中 i 为行数，j 为列数
     * - 矩阵乘积物理含义：
     *
     * | 买家\产品 | A | B | C |
     * | - | - | - | - |
     * | 甲 | 3 个 | 4 个 | 1 个 |
     * | 乙 | 4 个 | 1 个 | 2 个 |
     *
     * | 产品 | 价格 | 重量 |
     * | - | - | - |
     * | A | 5 元 | 10 斤 |
     * | B | 10 元 | 5 斤 |
     * | C | 9 元 | 6 斤 |
     *
     * | 买家 | 总花费 | 总重量 |
     * | - | - | - |
     * | 甲 | 64 | 56 |
     * | 乙 | 48 | 57 |
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array|false
     */
    public static function mmult($array1, $array2)
    {
        list($col1, $row1) = Arrays::colRowCount($array1);
        list($col2, $row2) = Arrays::colRowCount($array2);
        if ($col1 === $row2 && $row1 === $col2) {
            $arr = [];
            for ($r = 0; $r < $row1; $r++) {
                for ($c = 0; $c < $col2; $c++) {
                    $arr[$r][$c] = array_sum(array_map(function ($a1, $a2) {
                        return $a1 * $a2;
                    }, $array1[$r], Arrays::column($array2, $c)));
                }
            }
            return $arr;
        } else {
            return false;
        }
    }

    /**
     * 返回两数相除的余数
     *
     * @param integer $number 必需。 要计算余数的被除数
     * @param integer $divisor 必需。 除数
     *
     * @return integer
     */
    public static function mod($number, $divisor)
    {
        return $number % $divisor;
    }

    /**
     * 返回舍入到所需倍数的数字
     *
     * - 相比 Excel 的函数，此函数在符号不同时也能返回
     *
     * @param double $number 必需。 要舍入的值
     * @param double $multiple 必需。 要舍入到的倍数
     *
     * @return double
     */
    public static function mround($number, $multiple)
    {
        return round($number / $multiple) * $multiple;
    }

    /**
     * 返回参数和的阶乘与各参数阶乘乘积的比值
     *
     * @param double $number1 Number1 是必需的，后续数字是可选的
     *
     * @return double
     */
    public static function multinomial($number1)
    {
        $numbers = is_array($number1) ? $number1 : func_get_args();
        return self::fact(array_sum($numbers)) / array_product(array_map(function ($num) {
            return self::fact($num);
        }, $numbers));
    }

    /**
     * 返回指定维度的单位矩阵
     *
     * - 1NxN =
     * ```
     * 1 0 ... 0
     * 0 1 ... 0
     * ...   ...
     * 0 0 ... 1
     * ```
     * - 注：null 值在 php 里做运算时被看成 0，因此无需对 0 的元素进行赋值
     *
     * @param integer $dimension 必需。 Dimension 是一个整数, 指定要返回的单位矩阵的维度。 它返回一个数组。 维度必须大于零
     *
     * @return array
     */
    public static function munit($dimension)
    {
        $array = [];
        for ($i = 0; $i < $dimension; $i++) {
            $array[$i][$i] = 1;
        }
        return $array;
    }
}
