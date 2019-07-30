<?php
/**
 * Class Color
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

use Exception;
use icy2003\php\I;

/**
 * 颜色处理
 */
class Color
{
    /**
     * 颜色名
     *
     * @var array
     */
    protected static $_names = [
        'black' => 0x000000,
        'darkgreen' => 0x006400,
        'green' => 0x008000,
        'maroon' => 0x800000,
        'navy' => 0x000080,
        'myrtle' => 0x21421E,
        'bistre' => 0x3D2B1F,
        'darkblue' => 0x00008B,
        'darkred' => 0x8B0000,
        'sapphire' => 0x082567,
        'sangria' => 0x92000A,
        'burgundy' => 0x800020,
        'midnightblue' => 0x191970,
        'ultramarine' => 0x120A8F,
        'carmine' => 0x960018,
        'taupe' => 0x483C32,
        'chocolate' => 0x7B3F00,
        'auburn' => 0x6D351A,
        'sepia' => 0x704214,
        'arsenic' => 0x3B444B,
        'darkslategray' => 0x2F4F4F,
        'indigo' => 0x4B0082,
        'mediumblue' => 0x0000CD,
        'forestgreen' => 0x228B22,
        'charcoal' => 0x464646,
        'russet' => 0x80461B,
        'saddlebrown' => 0x8B4513,
        'carnelian' => 0xB31B1B,
        'liver' => 0x534B4F,
        'darkolivegreen' => 0x556B2F,
        'cobalt' => 0x0047AB,
        'eggplant' => 0x614051,
        'firebrick' => 0xB22222,
        'bole' => 0x79443B,
        'brown' => 0xA52A2A,
        'byzantium' => 0x702963,
        'feldgrau' => 0x4D5D53,
        'blue' => 0x0000FF,
        'lava' => 0xCF1020,
        'lime' => 0x00FF00,
        'red' => 0xFF0000,
        'mahogany' => 0xC04000,
        'olive' => 0x808000,
        'purple' => 0x800080,
        'teal' => 0x008080,
        'rust' => 0xB7410E,
        'cordovan' => 0x893F45,
        'darkslateblue' => 0x483D8B,
        'seagreen' => 0x2E8B57,
        'jade' => 0x00A86B,
        'darkcyan' => 0x008B8B,
        'darkmagenta' => 0x8B008B,
        'cardinal' => 0xC41E3A,
        'olivedrab' => 0x6B8E23,
        'sienna' => 0xA0522D,
        'cerulean' => 0x007BA7,
        'scarlet' => 0xFF2400,
        'crimson' => 0xDC143C,
        'viridian' => 0x40826D,
        'limegreen' => 0x32CD32,
        'denim' => 0x1560BD,
        'malachite' => 0x0BDA51,
        'dimgray' => 0x696969,
        'harlequin' => 0x3FFF00,
        'alizarin' => 0xE32636,
        'orangered' => 0xFF4500,
        'persimmon' => 0xEC5800,
        'darkgoldenrod' => 0xB8860B,
        'raspberry' => 0xE30B5C,
        'ruby' => 0xE0115F,
        'cinnabar' => 0xE34234,
        'cinnamon' => 0xD2691E,
        'vermilion' => 0xE34234,
        'copper' => 0xB87333,
        'amaranth' => 0xE52B50,
        'mediumseagreen' => 0x3CB371,
        'mediumvioletred' => 0xC71585,
        'red-violet' => 0xC71585,
        'ochre' => 0xCC7722,
        'darkviolet' => 0x9400D3,
        'xanadu' => 0x738678,
        'cerise' => 0xDE3163,
        'razzmatazz' => 0xE3256B,
        'asparagus' => 0x7BA05B,
        'tangerine' => 0xF28500,
        'lawngreen' => 0x7CFC00,
        'lightseagreen' => 0x20B2AA,
        'steelblue' => 0x4682B4,
        'bronze' => 0xCD7F32,
        'chartreuse' => 0x7FFF00,
        'rose' => 0xFF007F,
        'springgreen' => 0x00FF7F,
        'gray' => 0x808080,
        'slategray' => 0x708090,
        'chestnut' => 0xCD5C5C,
        'indianred' => 0xCD5C5C,
        'darkorange' => 0xFF8C00,
        'royalblue' => 0x4169E1,
        'pumpkin' => 0xFF7518,
        'gamboge' => 0xE49B0F,
        'emerald' => 0x50C878,
        'peru' => 0xCD853F,
        'slateblue' => 0x6A5ACD,
        'mediumspringgreen' => 0x00FA9A,
        'blueviolet' => 0x8A2BE2,
        'darkorchid' => 0x9932CC,
        'lightslategray' => 0x778899,
        'yellowgreen' => 0x9ACD32,
        'brass' => 0xB5A642,
        'cadetblue' => 0x5F9EA0,
        'darkturquoise' => 0x00CED1,
        'goldenrod' => 0xDAA520,
        'orange' => 0xFFA500,
        'deeppink' => 0xFF1493,
        'tomato' => 0xFF6347,
        'dodgerblue' => 0x1E90FF,
        'bluegreen' => 0x00DDDD,
        'amber' => 0xFFBF00,
        'deepskyblue' => 0x00BFFF,
        'fallow' => 0xC19A6B,
        'olivine' => 0x9AB973,
        'amethyst' => 0x9966CC,
        'turquoise' => 0x30D5C8,
        'coral' => 0xFF7F50,
        'mediumslateblue' => 0x7B68EE,
        'gold' => 0xFFD700,
        'darkseagreen' => 0x8FBC8F,
        'rosybrown' => 0xBC8F8F,
        'greenyellow' => 0xADFF2F,
        'mediumpurple' => 0x9370D8,
        'palevioletred' => 0xD87093,
        'mediumaquamarine' => 0x66CDAA,
        'darkkhaki' => 0xBDB76B,
        'mediumorchid' => 0xBA55D3,
        'pear' => 0xD1E231,
        'mediumturquoise' => 0x48D1CC,
        'cornflowerblue' => 0x6495ED,
        'saffron' => 0xF4C430,
        'salmon' => 0xFA8072,
        'puce' => 0xCC8899,
        'lightcoral' => 0xF08080,
        'ecru' => 0xC2B280,
        'lemon' => 0xFDE910,
        'sandybrown' => 0xF4A460,
        'darksalmon' => 0xE9967A,
        'darkgray' => 0xA9A9A9,
        'aqua' => 0x00FFFF,
        'cyan' => 0x00FFFF,
        'fuchsia' => 0xFF00FF,
        'magenta' => 0xFF00FF,
        'pink-orange' => 0xFF9966,
        'yellow' => 0xFFFF00,
        'lightgreen' => 0x90EE90,
        'tan' => 0xD2B48C,
        'lightsalmon' => 0xFFA07A,
        'hotpink' => 0xFF69B4,
        'burlywood' => 0xDEB887,
        'orchid' => 0xDA70D6,
        'palegreen' => 0x98FB98,
        'lilac' => 0xC8A2C8,
        'mustard' => 0xFFDB58,
        'celadon' => 0xACE1AF,
        'silver' => 0xC0C0C0,
        'skyblue' => 0x87CEEB,
        'corn' => 0xFBEC5D,
        'maize' => 0xFBEC5D,
        'wisteria' => 0xC9A0DC,
        'flax' => 0xEEDC82,
        'buff' => 0xF0DC82,
        'lightskyblue' => 0x87CEFA,
        'heliotrope' => 0xDF73FF,
        'aquamarine' => 0x7FFFD4,
        'lightsteelblue' => 0xB0C4DE,
        'plum' => 0xDDA0DD,
        'violet' => 0xEE82EE,
        'khaki' => 0xF0E68C,
        'peach-orange' => 0xFFCC99,
        'lightblue' => 0xADD8E6,
        'thistle' => 0xD8BFD8,
        'lightpink' => 0xFFB6C1,
        'powderblue' => 0xB0E0E6,
        'lightgrey' => 0xD3D3D3,
        'apricot' => 0xFBCEB1,
        'palegoldenrod' => 0xEEE8AA,
        'peach-yellow' => 0xFADFAD,
        'wheat' => 0xF5DEB3,
        'navajowhite' => 0xFFDEAD,
        'pink' => 0xFFC0CB,
        'paleturquoise' => 0xAFEEEE,
        'mauve' => 0xE0B0FF,
        'peachpuff' => 0xFFDAB9,
        'gainsboro' => 0xDCDCDC,
        'periwinkle' => 0xCCCCFF,
        'moccasin' => 0xFFE4B5,
        'peach' => 0xFFE5B4,
        'bisque' => 0xFFE4C4,
        'platinum' => 0xE5E4E2,
        'champaigne' => 0xF7E7CE,
        'blanchedalmond' => 0xFFEBCD,
        'antiquewhite' => 0xFAEBD7,
        'papayawhip' => 0xFFEFD5,
        'mistyrose' => 0xFFE4E1,
        'beige' => 0xF5F5DC,
        'lavender' => 0xE6E6FA,
        'lemonchiffon' => 0xFFFACD,
        'lightgoldenrodyellow' => 0xFAFAD2,
        'cream' => 0xFFFDD0,
        'linen' => 0xFAF0E6,
        'cornsilk' => 0xFFF8DC,
        'oldlace' => 0xFDF5E6,
        'lightcyan' => 0xE0FFFF,
        'lightyellow' => 0xFFFFE0,
        'honeydew' => 0xF0FFF0,
        'whitesmoke' => 0xF5F5F5,
        'seashell' => 0xFFF5EE,
        'lavenderblush' => 0xFFF0F5,
        'aliceblue' => 0xF0F8FF,
        'floralwhite' => 0xFFFAF0,
        'magnolia' => 0xF8F4FF,
        'azure' => 0xF0FFFF,
        'ivory' => 0xFFFFF0,
        'mintcream' => 0xF5FFFA,
        'ghostwhite' => 0xF8F8FF,
        'snow' => 0xFFFAFA,
        'white' => 0xFFFFFF,
    ];

    /**
     * 颜色模式
     *
     * @var string
     */
    protected $_type = 'unknow';

    /**
     * 颜色值
     *
     * @var mixed
     */
    protected $_color;

    /**
     * 自动类型：只支持 RGB、HEX、CMYK 的鉴别
     */
    const TYPE_AUTO = null;
    /**
     * RGB
     */
    const TYPE_RGB = 'rgb';
    /**
     * HEX
     */
    const TYPE_HEX = 'hex';
    /**
     * CMYK
     */
    const TYPE_CMYK = 'cmyk';

    /**
     * 创建颜色
     *
     * @param mixed $color 颜色值。
     * @param string $type 颜色类型
     *
     * - 如果给定 $type，则当前颜色被认作是 $type
     * - 支持三种颜色模式互转：十六进制、RGB、CMYK，也支持颜色名（'red'）转成这三种：
     *      1. 十六进制。传入十六进制数字（0xFF0000）或字符串（'FF0000'）
     *      2. RGB。传入三个元素的数组（[255, 0, 0]）
     *      3. CMYK。传入四个元素的数组（[0.0, 90.2, 82.9, 0.0]）
     */
    public function __construct($color, $type = self::TYPE_AUTO)
    {
        if (self::TYPE_AUTO === $type) {
            if (is_array($color)) {
                $this->_color = $color;
                if (3 === count($color)) {
                    $this->_type = self::TYPE_RGB;
                } elseif (4 === count($color)) {
                    $this->_type = self::TYPE_CMYK;
                }
            } else {
                // 接收十六进制（如：0xFF0000和'FF0000'）和颜色名字
                $hex = I::get(static::$_names, $color, $color);
                if ($hex > 0xFFFFFF || $hex < 0) {
                    throw new Exception('错误的颜色值：' . $color);
                }
                // 如果是字符形式的十六进制数，则先转成十进制再作后续运算
                if (is_string($hex)) {
                    $hex = hexdec($hex);
                }
                $this->_type = self::TYPE_HEX;
                $this->_color = $hex;
            }
            if ('unknow' === $this->_type) {
                throw new Exception('错误的颜色类型');
            }
        } else {
            $this->_color = $color;
            $this->_type = $type;
        }
    }

    /**
     * 转成 RGB
     *
     * @return static
     */
    public function toRGB()
    {
        $type = $this->_type;
        if (self::TYPE_RGB === $type) {
            $this->_color = array_map(function ($i) {
                return (0.5 + $i) | 0;
            }, $this->_color);
        } elseif (self::TYPE_HEX === $type) {
            $red = ($this->_color & 0xFF0000) >> 16;
            $green = ($this->_color & 0x00FF00) >> 8;
            $blue = ($this->_color & 0x0000FF);
            $this->_color = [$red, $green, $blue];
            $this->_type = self::TYPE_RGB;
            $this->toRGB();
        } elseif (self::TYPE_CMYK === $type) {
            $cyan = $this->_color[0] * (1 - $this->_color[3]) + $this->_color[3];
            $magenta = $this->_color[1] * (1 - $this->_color[3]) + $this->_color[3];
            $yellow = $this->_color[2] * (1 - $this->_color[3]) + $this->_color[3];
            $this->_color = [(1 - $cyan) * 255, (1 - $magenta) * 255, (1 - $yellow) * 255];
            $this->_type = self::TYPE_RGB;
            $this->toRGB();
        }

        return $this;
    }

    /**
     * 转成十六进制字符串
     *
     * @return static
     */
    public function toHex()
    {
        $type = $this->_type;
        if (self::TYPE_HEX === $type) {
            // 什么也不做
            $this->_type = self::TYPE_HEX;
        } elseif (self::TYPE_RGB === $type) {
            $this->_color = strtoupper(dechex($this->_color[0] << 16 | $this->_color[1] << 8 | $this->_color[2]));
            $this->_type = self::TYPE_HEX;
            $this->toHex();
        } elseif (self::TYPE_CMYK === $type) {
            $this->toRGB()->toHex();
        }

        return $this;
    }

    /**
     * 转成 CMYK
     *
     * @return static
     */
    public function toCMYK()
    {
        $type = $this->_type;
        if (self::TYPE_CMYK === $type) {
            $this->_color = array_map(function ($i) {
                return sprintf('%01.4f', $i);
            }, $this->_color);
            $this->_type = self::TYPE_CMYK;
        } elseif (self::TYPE_RGB === $type) {
            $cyan = 1 - ($this->_color[0] / 255);
            $magenta = 1 - ($this->_color[1] / 255);
            $yellow = 1 - ($this->_color[2] / 255);
            $var_K = 1;
            if ($cyan < $var_K) {
                $var_K = $cyan;
            }
            if ($magenta < $var_K) {
                $var_K = $magenta;
            }
            if ($yellow < $var_K) {
                $var_K = $yellow;
            }
            if ($var_K == 1) {
                $cyan = 0;
                $magenta = 0;
                $yellow = 0;
            } else {
                $cyan = ($cyan - $var_K) / (1 - $var_K);
                $magenta = ($magenta - $var_K) / (1 - $var_K);
                $yellow = ($yellow - $var_K) / (1 - $var_K);
            }

            $key = $var_K;
            $this->_color = [$cyan, $magenta, $yellow, $key];
            $this->_type = self::TYPE_CMYK;
            $this->toCMYK();
        } elseif (self::TYPE_HEX === $type) {
            $this->toRGB()->toCMYK();
        }

        return $this;
    }

    /**
     * 返回颜色值
     *
     * @return mixed
     */
    public function get()
    {
        return $this->_color;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return is_string($this->_color) ? $this->_color : implode(',', $this->_color);
    }
}
