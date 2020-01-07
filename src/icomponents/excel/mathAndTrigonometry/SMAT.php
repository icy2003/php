<?php
/**
 * Trait MathAndTrigonometry-S
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\excel\mathAndTrigonometry;

/**
 * MathAndTrigonometry-S
 */
trait SMAT
{
    /**
     * 返回角度的正割值
     *
     * - 定义：sec(x) = 1 / cos(x)，{ x | x ≠ k*π + π/2，k ∈ Z }
     *
     * @param double $number 必需。 Number 为需要对其进行正割的角度 (以弧度为单位)
     *
     * @return double
     */
    public static function sec($number)
    {
        return 1 / cos($number);
    }

    /**
     * 返回角度的双曲正割值
     *
     * - y = sech(x) = 1 / cosh(x) = 2 / (e^x + e^(-x))
     *
     * @param double $number 必需。 Number 为对应所需双曲正割值的角度，以弧度表示
     *
     * @return double
     */
    public static function sech($number)
    {
        return 1 / self::cosh($number);
    }

    /**
     * 返回基于以下公式的幂级数之和
     *
     * - Series(x, n, m, a) = a1 * x^n + a2 * x^(n + m) + a3 * x^(n + 2*m) + ... + ai * x^(n + (i - 1)*m)
     *
     * @param double $x 必需。 幂级数的输入值
     * @param double $n 必需。 x 的首项乘幂
     * @param double $m 必需。 级数中每一项的乘幂 n 的步长增加值
     * @param array $coefficients 必需。 与 x 的每个连续乘幂相乘的一组系数。 coefficients 中的值的数量决定了幂级数中的项数
     *
     * @return double
     */
    public static function seriessum($x, $n, $m, $coefficients)
    {
        $c = count($coefficients);
        $sum = 0;
        for ($i = 0; $i < $c; $i++) {
            $sum += $coefficients[$i] * pow($x, $n + $i * $m);
        }
        return $sum;
    }

    /**
     * 可在数组中生成一系列连续数字
     *
     * @param integer $row 要返回的行数
     * @param integer $col 要返回的列数
     * @param double $start 序列中第一个数字
     * @param double $step 数组中每个连续值递增的值
     *
     * @return array
     */
    public static function sequence($row, $col, $start = 1, $step = 1)
    {
        $array = [];
        $k = 1;
        for ($i = 0; $i < $row; $i++) {
            for ($j = 0; $j < $col; $j++) {
                $array[$i][$j] = $start + ($k++ -1) * $step;
            }
        }
        return $array;
    }

    /**
     * 确定数字的符号。 如果数字为正数，则返回 1；如果数字为 0，则返回零 (0)；如果数字为负数，则返回 -1
     *
     * @param double $number 必需。 任意实数
     *
     * @return integer
     */
    public static function sign($number)
    {
        return 0 == $number ? 0 : $number / abs($number);
    }

    /**
     * 返回已知角度的正弦
     *
     * @param double $number 必需。 需要求正弦的角度，以弧度表示
     *
     * @return double
     */
    public static function sin($number)
    {
        return sin($number);
    }

    /**
     * 返回数字的双曲正弦
     *
     * - y = sinh(x) = (e^x - e^(-x)) / 2;
     *
     * @param double $number 必需。 任意实数
     *
     * @return double
     */
    public static function sinh($number)
    {
        return sinh($number);
    }

    /**
     * 返回正的平方根
     *
     * @param double $number 必需。 要计算其平方根的数字
     *
     * @return double
     */
    public static function sqrt($number)
    {
        return sqrt($number);
    }

    /**
     * 返回某数与 pi 的乘积的平方根
     *
     * @param double $number 必需。 与 pi 相乘的数
     *
     * @return double
     */
    public static function sqrtpi($number)
    {
        return sqrt($number * pi());
    }

    /**
     * 返回列表或数据库中的分类汇总
     *
     * @todo
     *
     * @return void
     */
    public static function subtotal()
    {

    }

    /**
     * 可将值相加
     *
     * @param double|array $number1 要相加的第一个数字
     *
     * @return double
     */
    public static function sum($number1)
    {
        $numbers = is_array($number1) ? $number1 : func_get_args();
        return array_sum($numbers);
    }

    /**
     * 在给定的几组数组中，将数组间对应的元素相乘，并返回乘积之和
     *
     * - 注：office 页面的例子结果应该是 17.58 而不是 21.60
     *
     * @param array $arrays 必需。 其相应元素需要进行相乘并求和的第一个数组参数
     *
     * @return double
     */
    public static function sumproduct($arrays)
    {
        $arr = [];
        foreach ($arrays as $array) {
            $count = count($array);
            for ($i = 0; $i < $count; $i++) {
                empty($arr[$i]) && $arr[$i] = [];
                array_push($arr[$i], $array[$i]);
            }
        }
        return array_sum(array_map(function($rows) {
            return array_product($rows);
        }, $arr));
    }

    /**
     * 返回参数的平方和
     *
     * @param double $number1 Number1 是必需的，后续数字是可选的
     *
     * @return double
     */
    public static function sumsq($number1)
    {
        $numbers = is_array($number1) ? $number1 : func_get_args();
        return array_sum(array_map(function($num) {
            return $num * $num;
        }, $numbers));
    }

    /**
     * 返回两数组中对应数值的平方差之和
     *
     * - y = ∑(x^2 - y^2)
     *
     * @param array $arrayX 必需。 第一个数组或数值区域
     * @param array $arrayY 必需。 第二个数组或数值区域
     *
     * @return double
     */
    public static function sumx2my2($arrayX, $arrayY)
    {
        $count = count($arrayX);
        $sum = 0;
        for ($i = 0; $i < $count; $i++) {
            $sum += pow($arrayX[$i], 2) - pow($arrayY[$i], 2);
        }
        return $sum;
    }

    /**
     * 返回两数组中对应数值的平方和之和
     *
     * - y = ∑(x^2 + y^2)
     *
     * @param array $arrayX 必需。 第一个数组或数值区域
     * @param array $arrayY 必需。 第二个数组或数值区域
     *
     * @return double
     */
    public static function sumx2py2($arrayX, $arrayY)
    {
        $count = count($arrayX);
        $sum = 0;
        for ($i = 0; $i < $count; $i++) {
            $sum += pow($arrayX[$i], 2) + pow($arrayY[$i], 2);
        }
        return $sum;
    }

    /**
     * 返回两数组中对应数值之差的平方和
     *
     * - y = ∑(x - y)^2
     *
     * @param array $arrayX 必需。 第一个数组或数值区域
     * @param array $arrayY 必需。 第二个数组或数值区域
     *
     * @return double
     */
    public static function sumxmy2($arrayX, $arrayY)
    {
        $count = count($arrayX);
        $sum = 0;
        for ($i = 0; $i < $count; $i++) {
            $sum += pow($arrayX[$i] - $arrayY[$i], 2);
        }
        return $sum;
    }
}
