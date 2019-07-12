<?php
/**
 * Trait MathAndTrigonometry
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\icomponents\excel;

use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Strings;

/**
 * 数学和三角
 */
trait MathAndTrigonometry
{
    /**
     * 返回数字的绝对值
     *
     * - y = |x|, x ∈ (-∞, +∞)
     *
     * @param double  $number 必需。 需要计算其绝对值的实数
     *
     * @return double
     */
    public static function abs($number)
    {
        return abs($number);
    }

    /**
     * 返回数字的反余弦值
     *
     * - y = arccos(x), x ∈ [-1, 1]
     *
     * @param double $number 必需。 所求角度的余弦值，必须介于 -1 到 1 之间
     *
     * @return double
     */
    public static function acos($number)
    {
        return acos($number);
    }

    /**
     * 返回数字的反双曲余弦值
     *
     * - y = arcosh(x) = ln(x + √(x^2 -1)), x ∈ [1, +∞)
     *
     * @param double $number 必需。 大于或等于 1 的任意实数
     *
     * @return double
     */
    public static function acosh($number)
    {
        return acosh($number);
    }

    /**
     * 返回数字的反余切值的主值
     *
     * - y = arccot(x), x ∈ (-∞, +∞)
     *
     * @param double $number 必需。 Number 为所需角度的余切值。 此值必须是实数
     *
     * @return double
     */
    public static function acot($number)
    {
        return pi() / 2 - atan($number);
    }

    /**
     * 返回数字的反双曲余切值
     *
     * - y = arccoth(x) = arctanh(1 / x), {x| x > 1 或 x < -1}
     *
     * @param double $number 必需。 Number 的绝对值必须大于 1
     *
     * @return double
     */
    public static function acoth($number)
    {
        return atanh(1 / $number);
    }

    /**
     * 【数学和三角】返回列表或数据库中的合计。 AGGREGATE 函数可将不同的聚合函数应用于列表或数据库，并提供忽略隐藏行和错误值的选项。
     *
     * @todo
     *
     * @return void
     */
    public static function aggregate()
    {

    }

    /**
     * 将罗马数字转换为阿拉伯数字
     *
     * @param string $string 必需。 用引号引起的字符串、空字符串 ("") 或对包含文本的单元格的引用
     *
     * @return integer
     */
    public static function arabic($string)
    {
        $roman = array(
            'M' => 1000,
            'D' => 500,
            'C' => 100,
            'L' => 50,
            'X' => 10,
            'V' => 5,
            'I' => 1,
        );
        $strlen = Strings::length($string);
        $values = [];
        for ($i = 0; $i < $strlen; $i++) {
            if (isset($roman[strtoupper($string[$i])])) {
                $values[] = $roman[strtoupper($string[$i])];
            }
        }

        $sum = 0;
        while ($current = current($values)) {
            $next = next($values);
            $next > $current ? $sum += $next - $current + 0 * next($values) : $sum += $current;
        }
        return $sum;
    }

    /**
     * 返回数字的反正弦值
     *
     * - y = arcsin(x), x ∈ [-1, 1]
     *
     * @param double $number 必需。 所求角度的正弦值，必须介于 -1 到 1 之间
     *
     * @return double
     */
    public static function asin($number)
    {
        return asin($number);
    }

    /**
     * 返回数字的反双曲正弦值
     *
     * - y = arsinh(x) = ln(x + √(x^2 + 1)), x ∈ (-∞, +∞)
     *
     * @param double $number 必需。 任意实数
     *
     * @return double
     */
    public static function asinh($number)
    {
        return asinh($number);
    }

    /**
     * 返回数字的反正切值
     *
     * - y = arctan(x), x ∈(-π/2, π/2)
     *
     * @param double $number 必需。 所求角度的正切值
     *
     * @return double
     */
    public static function atan($number)
    {
        return atan($number);
    }

    /**
     * 返回给定的 X 轴及 Y 轴坐标值的反正切值
     *
     * @param double $x 必需。 点的 x 坐标
     * @param double $y 必需。 点的 y 坐标
     *
     * @return double
     */
    public static function atan2($x, $y)
    {
        $pi = 0;
        $sign = 1;
        if ($y < 0) {
            $sign = -1;
        }
        if ($x < 0) {
            $pi = pi();
        } elseif ($x == 0) {
            return pi() / 2 * $sign;
        }
        return $pi + $sign * self::atan(self::abs($y / $x));
    }

    /**
     * 返回数字的反双曲正切值
     *
     * - y = artanh(x), x ∈(-1, 1) = 1/2 * ln((1 + x) / (1 - x))
     *
     * @param double $number 必需。 -1 到 1 之间的任意实数
     *
     * @return double
     */
    public static function atanh($number)
    {
        return atanh($number);
    }

    /**
     * 将数字转换为具备给定基数的文本表示
     *
     * @param string $number 必需。 要转换的数字。 必须是大于或等于0且小于 2 ^ 53 的整数
     * @param integer $base 必需。 要将数字转换为的基础基数。 必须是大于或等于2且小于或等于36的整数
     * @param integer $minLength 可选。 返回的字符串的最小长度。 必须是大于或等于0的整数
     *
     * @return string
     */
    public static function base($number, $base, $minLength = null)
    {
        $string = base_convert($number, 10, $base);
        if (null === $minLength) {
            return $string;
        } else {
            return str_pad($string, $minLength, STR_PAD_LEFT);
        }
    }

    /**
     * 返回将参数 number 向上舍入（沿绝对值增大的方向）为最接近的指定基数的倍数
     *
     * @param double $number  必需。 要舍入的值
     * @param double $significance 必需。 要舍入到的倍数
     *
     * @return double
     */
    public static function ceiling($number, $significance = 1)
    {
        return is_numeric($number) ? (ceil($number / $significance) * $significance) : false;
    }

    /**
     * 返回给定数目的项目的组合数
     *
     * - C(m,n) = n! / (m! * (n - m)!)，其中 0 ≤ m ≤ n
     *
     * @param integer $n 必需。 项目的数量
     * @param integer $m 必需。 每一组合中项目的数量
     *
     * @return integer
     */
    public static function combin($n, $m)
    {
        return (self::fact($n) / (self::fact($m) * self::fact($n - $m)));
    }

    /**
     * 返回给定数目的项的组合数（包含重复）
     *
     * @param integer $n 必需。 必须大于或等于 0 并大于或等于 m 非整数值将被截尾取整
     * @param integer $m 必需。 必须大于或等于 0。 非整数值将被截尾取整
     *
     * @return integer
     */
    public static function combina($n, $m)
    {
        return self::combin($n + $m - 1, $n - 1);
    }

    /**
     * 返回已知角度的余弦值
     *
     * @param double $number 必需。 想要求余弦的角度，以弧度表示
     *
     * @return double
     */
    public static function cos($number)
    {
        return cos($number);
    }

    /**
     * 返回数字的双曲余弦值
     *
     * - y = cosh(x) = (e^x + e^(-x)) / 2
     *
     * @param double $number 必需。 想要求双曲余弦的任意实数
     *
     * @return double
     */
    public static function cosh($number)
    {
        return cosh($number);
    }

    /**
     * 返回以弧度表示的角度的余切值
     *
     * @param double $number 必需。 要获得其余切值的角度，以弧度表示
     *
     * @return double
     */
    public static function cot($number)
    {
        return -tan(pi() / 2 + $number);
    }

    /**
     * 返回一个双曲角度的双曲余切值
     *
     * - y = coth(x) = 1 / tanh(x) = (e^x + e^(-x)) / (e^x - e^(-x))
     *
     * @param double $number 必需
     *
     * @return double
     */
    public static function coth($number)
    {
        return 1 / tanh($number);
    }

    /**
     * 返回角度的余割值，以弧度表示
     *
     * @param double $number 必需
     *
     * @return double
     */
    public static function csc($number)
    {
        return 1 / sin($number);
    }

    /**
     * 返回角度的双曲余割值，以弧度表示
     *
     * - y = csch(x) = 1 / sinh(x) = 2 / (e^x - e^(-x))
     *
     * @param double $number 必需
     *
     * @return double
     */
    public static function csch($number)
    {
        return 1 / sinh($number);
    }

    /**
     * 按给定基数将数字的文本表示形式转换成十进制数
     *
     * @param string $text 必需
     * @param integer $radix 必需。Radix 必须是整数
     *
     * @return string
     */
    public static function decimal($text, $radix)
    {
        return base_convert($text, $radix, 10);
    }

    /**
     * 将弧度转换为度
     *
     * @param double $angle
     *
     * @return double
     */
    public static function degrees($angle)
    {
        return rad2deg($angle);
    }

    /**
     * 返回数字向上舍入到的最接近的偶数
     *
     * @param double $number 必需。 要舍入的值
     *
     * @return integer
     */
    public static function even($number)
    {
        $number = ceil($number);
        if ($number % 2 == 1) {
            $number += 1;
        }
        return (int) $number;
    }

    /**
     * 返回 e 的 n 次幂。 常数 e 等于 2.71828182845904，是自然对数的底数
     *
     * @param integer $number
     *
     * @return double
     */
    public static function exp($number)
    {
        return exp($number);
    }

    /**
     * 返回数的阶乘
     *
     * @param integer|double $number 必需。 要计算其阶乘的非负数。 如果 number 不是整数，将被截尾取整
     *
     * @return integer
     */
    public static function fact($number)
    {
        $number = (int) floor($number);
        if ($number == 0) {
            return 1;
        }
        $result = 1;
        foreach (Arrays::rangeGenerator(1, $number) as $num) {
            $result *= $num;
        }
        return $result;
    }

    /**
     * 返回数字的双倍阶乘
     *
     * - 偶数：n!! = n * (n - 2) * (n - 4) * ... * 4 * 2
     * - 奇数：n!! = n * (n - 2) * (n - 4) * ... * 3 * 1
     *
     * @param integer $number 必需。 为其返回双倍阶乘的值。 如果 number 不是整数，将被截尾取整
     *
     * @return integer
     */
    public static function factdouble($number)
    {
        $number = floor($number);
        if ($number == 0) {
            return 1;
        }
        $result = 1;
        $isEven = $number % 2 == 0;
        foreach (Arrays::rangeGenerator($isEven ? 2 : 1, $number, 2) as $num) {
            $result *= $num;
        }
        return $result;
    }

    /**
     * 将参数 number 向下舍入（沿绝对值减小的方向）为最接近的 significance 的倍数
     *
     * @param double $number 必需。 要舍入的数值
     * @param double $significance 必需。 要舍入到的倍数
     *
     * @return double
     */
    public static function floor($number, $significance = 1)
    {
        return (is_numeric($number) && is_numeric($significance)) ? (floor($number / $significance) * $significance) : false;
    }

    /**
     * 返回两个或多个整数的最大公约数。 最大公约数是能够同时整除 number1 和 number2 而没有余数的最大整数
     *
     * @param integer|array $number1 Number1 是必需的，后续数字是可选的。 介于 1 和 255 之间的值
     *
     * @return integer
     */
    public static function gcd($number1)
    {
        $numbers = is_array($number1) ? $number1 : func_get_args();
        $n = min($numbers);
        if (in_array(0, $numbers)) {
            return max($numbers);
        }
        for ($i = $n; $i > 1; $i--) {
            $isFind = true;
            foreach ($numbers as $num) {
                if (false === is_int($num / $i)) {
                    $isFind = false;
                    break;
                }
            }
            if (true === $isFind) {
                return $i;
            }
        }
        return 1;
    }

    /**
     * 将数字向下舍入到最接近的整数
     *
     * - int 是关键字，这里用上后缀 _i
     *
     * @param double $number 必需。 需要进行向下舍入取整的实数
     *
     * @return integer
     */
    public static function int_i($number)
    {
        return floor($number);
    }

    /**
     * 返回整数的最小公倍数。 最小公倍数是所有整数参数 number1、number2 等的倍数中的最小正整数。 使用 LCM 添加具有不同分母的分数
     *
     * @param integer|array $number1 Number1 是必需的，后续数字是可选的。 要计算其最小公倍数的 1 到 255 个值
     *
     * @return integer
     */
    public static function lcm($number1)
    {
        $numbers = is_array($number1) ? $number1 : func_get_args();
        if (in_array(0, $numbers)) {
            return 0;
        }
        return array_product($numbers) / self::gcd($numbers);
    }

    /**
     * 返回数字的自然对数。 自然对数以常数 e (2.71828182845904) 为底
     *
     * @param double $number 必需。 想要计算其自然对数的正实数
     *
     * @return double
     */
    public static function ln($number)
    {
        return log($number);
    }

    /**
     * 根据指定底数返回数字的对数
     *
     * - 注意：此和 PHP 的 log 的默认底不一样，PHP 的默认底是自然对数 e
     *
     * @param double $number 必需。 想要计算其对数的正实数
     * @param double $base 可选。 对数的底数。 如果省略 base，则假定其值为 10
     *
     * @return double
     */
    public static function log($number, $base = 10)
    {
        return log($number, $base);
    }

    /**
     * 返回数字以 10 为底的对数
     *
     * @param double $number 必需。 想要计算其以 10 为底的对数的正实数
     *
     * @return double
     */
    public static function log10($number)
    {
        return log($number, 10);
    }

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
     * @return void
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

    /**
     * 返回数字向上舍入到的最接近的奇数
     *
     * @param double $number 必需。 要舍入的值
     *
     * @return integer
     */
    public static function odd($number)
    {
        $number = ceil($number);
        if ($number % 2 == 0) {
            $number += 1;
        }
        return (int) $number;
    }

    /**
     * 数学常量 pi
     *
     * @return double
     */
    public static function pi()
    {
        return pi();
    }

    /**
     * 返回数字乘幂的结果
     *
     * @param double $number  必需。 基数。 可为任意实数
     * @param double $power 必需。 基数乘幂运算的指数
     *
     * @return double
     */
    public static function power($number, $power)
    {
        return pow($number, $power);
    }

    /**
     * PRODUCT函数将以参数形式给出的所有数字相乘
     *
     * @param double $number1 必需。 要相乘的第一个数字或范围
     *
     * @return double
     */
    public static function product($number1)
    {
        $numbers = is_array($number1) ? $number1 : func_get_args();
        return array_product($numbers);
    }

    /**
     * 返回除法的整数部分。 要放弃除法的余数时，可使用此函数
     *
     * @param double $numberator 必需。 被除数
     * @param double $denominator 必需。 除数
     *
     * @return integer
     */
    public static function quotient($numberator, $denominator)
    {
        return (int) ($numberator / $denominator);
    }

    /**
     * 将度数转换为弧度
     *
     * @param double $angle 必需。 要转换的以度数表示的角度
     *
     * @return double
     */
    public static function radians($angle)
    {
        return deg2rad($angle);
    }

    /**
     * 返回了一个大于等于 0 且小于 1 的平均分布的随机实数
     *
     * @return double
     */
    public static function rand()
    {
        return mt_rand(0, (int) self::power(10, 10)) / self::power(10, 10);
    }

    /**
     * 函数返回一组随机数字。 可指定要填充的行数和列数，最小值和最大值，以及是否返回整数或小数值
     *
     * @param integer $row 要返回的行数
     * @param integer $col 要返回的列数
     * @param integer $min 想返回的最小数值
     * @param integer $max 想返回的最大数值
     * @param boolean $isInt 返回整数或小数
     *
     * @return array
     */
    public static function randarray($row = 1, $col = 1, $min = 0, $max = null, $isInt = false)
    {
        null === $max && $max = (int) pow(10, 10);
        $array = [];
        for ($i = 0; $i < $row; $i++) {
            for ($j = 0; $j < $col; $j++) {
                $array[$i][$j] = mt_rand($min, $max) + ($isInt ? 0 : mt_rand(0, (int) pow(10, 10)) / pow(10, 10));
            }
        }
        return $array;
    }

    /**
     * 返回位于两个指定数之间的一个随机整数
     *
     * @param integer $bottom 必需。 RANDBETWEEN 将返回的最小整数
     * @param integer $top 必需。 RANDBETWEEN 将返回的最大整数
     *
     * @return integer
     */
    public static function randbetween($bottom, $top)
    {
        return mt_rand($bottom, $top);
    }

    /**
     * 将阿拉伯数字转换为文字形式的罗马数字
     *
     * - 注意：不支持简明版
     *
     * @param integer $number
     *
     * @return string|false
     */
    public static function roman($number)
    {
        if (!is_numeric($number) || $number > 3999 || $number <= 0) {
            return false;
        }

        $roman = array(
            'M' => 1000,
            'D' => 500,
            'C' => 100,
            'L' => 50,
            'X' => 10,
            'V' => 5,
            'I' => 1,
        );
        foreach ($roman as $k => $v) {
            if (($amount[$k] = floor($number / $v)) > 0) {
                $number -= $amount[$k] * $v;
            }
        }

        // Build the string:
        $return = '';
        $oldK = '';
        foreach ($amount as $k => $v) {
            $return .= $v <= 3 ? str_repeat($k, $v) : $k . $oldK;
            $oldK = $k;
        }
        return str_replace(array('VIV', 'LXL', 'DCD'), array('IX', 'XC', 'CM'), $return);
    }

    /**
     * 函数将数字四舍五入到指定的位数
     *
     * @param double $number 必需。 要四舍五入的数字
     * @param integer $digits 要进行四舍五入运算的位数
     *
     * @return double
     */
    public static function round($number, $digits = 0)
    {
        return round($number, $digits);
    }

    /**
     * 朝着零的方向将数字进行向下舍入
     *
     * @param double $number 必需。需要向下舍入的任意实数
     * @param integer $digits 要将数字舍入到的位数
     *
     * @return double
     */
    public static function rounddown($number, $digits = 0)
    {
        $sign = $number >= 0 ? 1 : -1;
        return round(abs($number) - 0.5 * pow(10, -$digits), $digits) * $sign;
    }

    /**
     * 朝着远离 0（零）的方向将数字进行向上舍入
     *
     * @param double $number 必需。需要向下舍入的任意实数
     * @param integer $digits 要将数字舍入到的位数
     *
     * @return double
     */
    public static function roundup($number, $digits = 0)
    {
        $sign = $number >= 0 ? 1 : -1;
        return round(abs($number) + 0.5 * pow(10, -$digits), $digits) * $sign;
    }

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
            $sum += $coefficients[$i] * pow($x, $n + ($i - 1) * $m);
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
                $array[$i][$j] = $start + ($k++ - 1) * $step;
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
    public static function sqrti($number)
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
     * @param double $number1 要相加的第一个数字
     *
     * @return double
     */
    public static function sum($number1)
    {
        $numbers = is_array($number1) ? $number1 : func_get_args();
        return array_sum($numbers);
    }

    /**
     * 可以使用 SUMIF 函数对 范围 中符合指定条件的值求和
     *
     * - 注意：为保证安全性，不提供此函数
     *
     * @return false
     */
    public static function sumif()
    {
        return false;
    }

    /**
     * 用于计算其满足多个条件的全部参数的总量
     *
     * - 注意：为保证安全性，不提供此函数
     *
     * @return false
     */
    public static function sumifs()
    {
        return false;
    }

    /**
     * 在给定的几组数组中，将数组间对应的元素相乘，并返回乘积之和
     *
     * - 注：office 页面的例子结果应该是 17.58 而不是 21.60
     *
     * @param array $array1 必需。 其相应元素需要进行相乘并求和的第一个数组参数
     *
     * @return double
     */
    public static function sumproduct($array1)
    {
        $arrays = is_array($array1) ? $array1 : func_get_args();
        $arr = [];
        foreach ($arrays as $array) {
            $count = count($array);
            for ($i = 0; $i < $count; $i++) {
                empty($arr[$i]) && $arr[$i] = [];
                array_push($arr[$i], $array[$i]);
            }
        }
        return array_sum(array_map(function ($rows) {
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
        return array_sum(array_map(function ($num) {
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

    /**
     * 返回已知角度的正切
     *
     * @param double $number 必需。 要求正切的角度，以弧度表示
     *
     * @return double
     */
    public static function tan($number)
    {
        return tan($number);
    }

    /**
     * 返回数字的双曲正切
     *
     * - y = sinh(x) / cosh(x) = (e^x - e^(-x)) / (e^x + e^(-x))
     *
     * @param double $number 必需。 任意实数
     *
     * @return double
     */
    public static function tanh($number)
    {
        return tanh($number);
    }

    /**
     * 将数字的小数部分截去，返回整数
     *
     * @param double $number 必需。 需要截尾取整的数字
     * @param integer $digits 可选。 用于指定取整精度的数字。 num_digits 的默认值为 0（零）
     *
     * @return double
     */
    public static function trunc($number, $digits = 0)
    {
        $sign = self::sign($number);
        return round(abs($number) - 0.5, $digits) * $sign;
    }
}
