<?php
/**
 * Class Convert
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

/**
 * 转化类
 *
 * @method string Convert::f2t8(string $number) 从二进制转到八进制
 * @method string Convert::f2t10(string $number) 从二进制转到十进制
 * @method string Convert::f2t16(string $number) 从二进制转到十六进制
 * @method string Convert::f8t2(string $number) 从八进制转到二进制
 * @method string Convert::f8t10(string $number) 从八进制转到十进制
 * @method string Convert::f8t16(string $number) 从八进制转到十六进制
 * @method string Convert::f16t2(string $number) 从十六进制转到二进制
 * @method string Convert::f16t8(string $number) 从十六进制转到八进制
 * @method string Convert::f16t10(string $number) 从十六进制转到十进制
 */
class Convert
{

    /**
     * 默认支持的进制转化配置
     *
     * @var array
     */
    protected static $_N = [
        '2' => '01',
        '8' => '01234567',
        '10' => '0123456789',
        '16' => '0123456789ABCDEF',
    ];

    /**
     * 其他数量转化为字节数
     *
     * @param string $sizeValue 大小。例如：10m、10M、10Tb、10kB 等
     *
     * @return string
     */
    public static function size($sizeValue)
    {
        $callback = function ($matches) {
            $sizeMap = [
                '' => 0,
                'b' => 0, // 为了简化正则
                 'k' => 1,
                'm' => 2,
                'g' => 3,
                't' => 4,
                'p' => 5,
            ];

            return $matches[1] * pow(1024, $sizeMap[strtolower($matches[2])]);
        };

        return preg_replace_callback('/(\d*)([a-z]?)b?/i', $callback, $sizeValue, 1);
    }

    /**
     * 将某进制的字符串转化成另一进制的字符串
     * @see http://php.net/manual/zh/function.base-convert.php
     *
     * @param string $numberInput 待转换的字符串
     * @param string $fromBaseInput 起始进制的规则
     * @param string $toBaseInput 结束进制的规则
     *
     * @return string
     */
    public static function baseConvert($numberInput, $fromBaseInput, $toBaseInput)
    {
        if ($fromBaseInput == $toBaseInput) {
            return $numberInput;
        }

        $fromBase = str_split($fromBaseInput, 1);
        $toBase = str_split($toBaseInput, 1);
        $number = str_split($numberInput, 1);
        $fromLen = strlen($fromBaseInput);
        $toLen = strlen($toBaseInput);
        $numberLen = strlen($numberInput);
        $retval = '';
        if ($toBaseInput == '0123456789') {
            $retval = 0;
            for ($i = 1; $i <= $numberLen; $i++) {
                $retval = bcadd($retval, bcmul(array_search($number[$i - 1], $fromBase), bcpow($fromLen, $numberLen - $i)));
            }

            return $retval;
        }
        if ($fromBaseInput != '0123456789') {
            $base10 = self::baseConvert($numberInput, $fromBaseInput, '0123456789');
        } else {
            $base10 = $numberInput;
        }

        if ($base10 < strlen($toBaseInput)) {
            return $toBase[$base10];
        }

        while ($base10 != '0') {
            $retval = $toBase[bcmod($base10, $toLen)] . $retval;
            $base10 = bcdiv($base10, $toLen, 0);
        }
        return $retval;
    }

    /**
     * 2、8、10、16 进制转化的魔术方法
     *
     * @param string $name 静态方法名
     * @param array $arguments 参数
     *
     * @return string
     */
    public static function __callStatic($name, $arguments)
    {
        if (1 !== count($arguments)) {
            return false;
        }
        $matches = null;
        if (preg_match('/^f(\d{1,2})t(\d{1,2})$/', $name, $matches)) {
            if (3 === count($matches)) {
                if (in_array($matches[1], [2, 8, 10, 16]) && in_array($matches[2], [2, 8, 10, 16])) {
                    return self::baseConvert($arguments[0], static::$_N[$matches[1]], static::$_N[$matches[2]]);
                }
            }
        }
        return false;
    }

}
