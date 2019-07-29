<?php
/**
 * Trait Strings
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

use Exception;

/**
 * 字符串扩展
 */
trait StringsTrait
{
    /**
     * 生成密码 hash
     *
     * @param string $password 原始密码
     * @param integer $cost
     *
     * @return string
     */
    public static function generatePasswordHash($password, $cost = null)
    {
        null === $cost && $cost = 13;
        // PHP 5 >= 5.5.0, PHP 7
        if (function_exists('password_hash')) {
            return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
        }
        if ($cost < 4 || $cost > 31) {
            throw new Exception('cost 必须大于等于 4，小于等于 31');
        }
        $salt = sprintf('$2y$%02d$', $cost);
        $salt .= str_replace('+', '.', substr(base64_encode(self::random(20)), 0, 22));
        $hash = crypt($password, $salt);
        if (!is_string($hash) || strlen($hash) !== 60) {
            throw new Exception('未知错误');
        }
        return $hash;
    }

    /**
     * 验证密码
     *
     * @param string $password 原始密码
     * @param string $hash HASH 后的密码，需配合 Strings::generatePasswordHash
     *
     * @return boolean
     */
    public static function validatePassword($password, $hash)
    {
        if (!is_string($password) || $password === '') {
            return false;
        }
        $matches = [];
        if (!preg_match('/^\$2[axy]\$(\d\d)\$[\.\/0-9A-Za-z]{22}/', $hash, $matches)
            || $matches[1] < 4
            || $matches[1] > 30) {
            return false;
        }
        // PHP 5 >= 5.5.0, PHP 7
        if (function_exists('password_verify')) {
            return password_verify($password, $hash);
        }

        $test = crypt($password, $hash);
        $n = strlen($test);
        if ($n !== 60) {
            return false;
        }
        // PHP 5 >= 5.6.0, PHP 7
        if (function_exists('hash_equals')) {
            return hash_equals($test, $hash);
        }

        $test .= '\0';
        $hash .= '\0';
        $expectedLength = self::byteLength($test);
        $actualLength = self::byteLength($hash);
        $diff = $expectedLength - $actualLength;
        for ($i = 0; $i < $actualLength; $i++) {
            $diff |= (ord($hash[$i]) ^ ord($test[$i % $expectedLength]));
        }

        return $diff === 0;
    }

    /**
     * 字符串转成变量
     *
     * - 变量是被如：{{}}括起来的字符串
     * - 例如：{{var}}
     *
     * @param string $name
     * @param array $boundary 边界符，默认 ['{{', '}}']
     *
     * @return string
     */
    public static function toVariable($name, $boundary = ['{{', '}}'])
    {
        if (is_string($boundary)) {
            $boundary = [$boundary, $boundary];
        }
        return self::isContains($name, $boundary[0]) ? $name : $boundary[0] . $name . $boundary[1];
    }

    /**
     * 判断一个字符串是否是变量
     *
     * @param string $name
     * @param array $boundary 边界符，默认 ['{{', '}}']
     *
     * @return boolean
     */
    public static function isVariable($name, $boundary = ['{{', '}}'])
    {
        return self::isStartsWith($name, $boundary[0]) && self::isEndsWith($name, $boundary[1]);
    }

    /**
     * 计算包含变量的字符串
     *
     * @param string $text
     * @param array $array 键值对形式：[{{键}} => 值]。如果键不是变量，则不会替换进 $text
     * @param array $boundary 边界符，默认 ['{{', '}}']
     *
     * @return string
     */
    public static function fromVariable($text, $array, $boundary = ['{{', '}}'])
    {
        $data = [];
        foreach ($array as $name => $value) {
            self::isVariable($name, $boundary) && $data[$name] = $value;
        }
        return str_replace(array_keys($data), array_values($data), $text);
    }

    /**
     * 判断两个字符串像不像
     *
     * - 图形验证码里经常有人把 o 看成 0，所以……
     * - 例如：hello 和 hell0 看起来是像的 (-w-)o~
     *
     * @param string $string1 第一个字符串
     * @param string $string2 第二个字符串
     * @param array $array 看起来像的字符的列表，默认 ['0o', 'yv', 'ij', '1l']
     *
     * @return boolean
     */
    public static function looksLike($string1, $string2, $array = ['0oO', 'yv', 'ij', '1lI'])
    {
        $array1 = self::split($string1);
        $array2 = self::split($string2);
        foreach ($array1 as $index => $char1) {
            $char1 = strtolower($char1);
            $char2 = strtolower($array2[$index]);
            $isEqual = false;
            if ($char1 == $char2) {
                $isEqual = true;
            }
            foreach ($array as $row) {
                if (self::isContains($row, $char1) && self::isContains($row, $char2)) {
                    $isEqual = true;
                    break;
                }
            }
            if (false === $isEqual) {
                break;
            }
        }
        return $isEqual;
    }

    /**
     * 拼音编码对应表
     *
     * @var array
     */
    private static $__pinyin = ['a' => -20319, 'ai' => -20317, 'an' => -20304, 'ang' => -20295, 'ao' => -20292, 'ba' => -20283, 'bai' => -20265, 'ban' => -20257, 'bang' => -20242, 'bao' => -20230, 'bei' => -20051, 'ben' => -20036, 'beng' => -20032, 'bi' => -20026, 'bian' => -20002, 'biao' => -19990, 'bie' => -19986, 'bin' => -19982, 'bing' => -19976, 'bo' => -19805, 'bu' => -19784, 'ca' => -19775, 'cai' => -19774, 'can' => -19763, 'cang' => -19756, 'cao' => -19751, 'ce' => -19746, 'ceng' => -19741, 'cha' => -19739, 'chai' => -19728, 'chan' => -19725, 'chang' => -19715, 'chao' => -19540, 'che' => -19531, 'chen' => -19525, 'cheng' => -19515, 'chi' => -19500, 'chong' => -19484, 'chou' => -19479, 'chu' => -19467, 'chuai' => -19289, 'chuan' => -19288, 'chuang' => -19281, 'chui' => -19275, 'chun' => -19270, 'chuo' => -19263, 'ci' => -19261, 'cong' => -19249, 'cou' => -19243, 'cu' => -19242, 'cuan' => -19238, 'cui' => -19235, 'cun' => -19227, 'cuo' => -19224, 'da' => -19218, 'dai' => -19212, 'dan' => -19038, 'dang' => -19023, 'dao' => -19018, 'de' => -19006, 'deng' => -19003, 'di' => -18996, 'dian' => -18977, 'diao' => -18961, 'die' => -18952, 'ding' => -18783, 'diu' => -18774, 'dong' => -18773, 'dou' => -18763, 'du' => -18756, 'duan' => -18741, 'dui' => -18735, 'dun' => -18731, 'duo' => -18722, 'e' => -18710, 'en' => -18697, 'er' => -18696, 'fa' => -18526, 'fan' => -18518, 'fang' => -18501, 'fei' => -18490, 'fen' => -18478, 'feng' => -18463, 'fo' => -18448, 'fou' => -18447, 'fu' => -18446, 'ga' => -18239, 'gai' => -18237, 'gan' => -18231, 'gang' => -18220, 'gao' => -18211, 'ge' => -18201, 'gei' => -18184, 'gen' => -18183, 'geng' => -18181, 'gong' => -18012, 'gou' => -17997, 'gu' => -17988, 'gua' => -17970, 'guai' => -17964, 'guan' => -17961, 'guang' => -17950, 'gui' => -17947, 'gun' => -17931, 'guo' => -17928, 'ha' => -17922, 'hai' => -17759, 'han' => -17752, 'hang' => -17733, 'hao' => -17730, 'he' => -17721, 'hei' => -17703, 'hen' => -17701, 'heng' => -17697, 'hong' => -17692, 'hou' => -17683, 'hu' => -17676, 'hua' => -17496, 'huai' => -17487, 'huan' => -17482, 'huang' => -17468, 'hui' => -17454, 'hun' => -17433, 'huo' => -17427, 'ji' => -17417, 'jia' => -17202, 'jian' => -17185, 'jiang' => -16983, 'jiao' => -16970, 'jie' => -16942, 'jin' => -16915, 'jing' => -16733, 'jiong' => -16708, 'jiu' => -16706, 'ju' => -16689, 'juan' => -16664, 'jue' => -16657, 'jun' => -16647, 'ka' => -16474, 'kai' => -16470, 'kan' => -16465, 'kang' => -16459, 'kao' => -16452, 'ke' => -16448, 'ken' => -16433, 'keng' => -16429, 'kong' => -16427, 'kou' => -16423, 'ku' => -16419, 'kua' => -16412, 'kuai' => -16407, 'kuan' => -16403, 'kuang' => -16401, 'kui' => -16393, 'kun' => -16220, 'kuo' => -16216, 'la' => -16212, 'lai' => -16205, 'lan' => -16202, 'lang' => -16187, 'lao' => -16180, 'le' => -16171, 'lei' => -16169, 'leng' => -16158, 'li' => -16155, 'lia' => -15959, 'lian' => -15958, 'liang' => -15944, 'liao' => -15933, 'lie' => -15920, 'lin' => -15915, 'ling' => -15903, 'liu' => -15889, 'long' => -15878, 'lou' => -15707, 'lu' => -15701, 'lv' => -15681, 'luan' => -15667, 'lue' => -15661, 'lun' => -15659, 'luo' => -15652, 'ma' => -15640, 'mai' => -15631, 'man' => -15625, 'mang' => -15454, 'mao' => -15448, 'me' => -15436, 'mei' => -15435, 'men' => -15419, 'meng' => -15416, 'mi' => -15408, 'mian' => -15394, 'miao' => -15385, 'mie' => -15377, 'min' => -15375, 'ming' => -15369, 'miu' => -15363, 'mo' => -15362, 'mou' => -15183, 'mu' => -15180, 'na' => -15165, 'nai' => -15158, 'nan' => -15153, 'nang' => -15150, 'nao' => -15149, 'ne' => -15144, 'nei' => -15143, 'nen' => -15141, 'neng' => -15140, 'ni' => -15139, 'nian' => -15128, 'niang' => -15121, 'niao' => -15119, 'nie' => -15117, 'nin' => -15110, 'ning' => -15109, 'niu' => -14941, 'nong' => -14937, 'nu' => -14933, 'nv' => -14930, 'nuan' => -14929, 'nue' => -14928, 'nuo' => -14926, 'o' => -14922, 'ou' => -14921, 'pa' => -14914, 'pai' => -14908, 'pan' => -14902, 'pang' => -14894, 'pao' => -14889, 'pei' => -14882, 'pen' => -14873, 'peng' => -14871, 'pi' => -14857, 'pian' => -14678, 'piao' => -14674, 'pie' => -14670, 'pin' => -14668, 'ping' => -14663, 'po' => -14654, 'pu' => -14645, 'qi' => -14630, 'qia' => -14594, 'qian' => -14429, 'qiang' => -14407, 'qiao' => -14399, 'qie' => -14384, 'qin' => -14379, 'qing' => -14368, 'qiong' => -14355, 'qiu' => -14353, 'qu' => -14345, 'quan' => -14170, 'que' => -14159, 'qun' => -14151, 'ran' => -14149, 'rang' => -14145, 'rao' => -14140, 're' => -14137, 'ren' => -14135, 'reng' => -14125, 'ri' => -14123, 'rong' => -14122, 'rou' => -14112, 'ru' => -14109, 'ruan' => -14099, 'rui' => -14097, 'run' => -14094, 'ruo' => -14092, 'sa' => -14090, 'sai' => -14087, 'san' => -14083, 'sang' => -13917, 'sao' => -13914, 'se' => -13910, 'sen' => -13907, 'seng' => -13906, 'sha' => -13905, 'shai' => -13896, 'shan' => -13894, 'shang' => -13878, 'shao' => -13870, 'she' => -13859, 'shen' => -13847, 'sheng' => -13831, 'shi' => -13658, 'shou' => -13611, 'shu' => -13601, 'shua' => -13406, 'shuai' => -13404, 'shuan' => -13400, 'shuang' => -13398, 'shui' => -13395, 'shun' => -13391, 'shuo' => -13387, 'si' => -13383, 'song' => -13367, 'sou' => -13359, 'su' => -13356, 'suan' => -13343, 'sui' => -13340, 'sun' => -13329, 'suo' => -13326, 'ta' => -13318, 'tai' => -13147, 'tan' => -13138, 'tang' => -13120, 'tao' => -13107, 'te' => -13096, 'teng' => -13095, 'ti' => -13091, 'tian' => -13076, 'tiao' => -13068, 'tie' => -13063, 'ting' => -13060, 'tong' => -12888, 'tou' => -12875, 'tu' => -12871, 'tuan' => -12860, 'tui' => -12858, 'tun' => -12852, 'tuo' => -12849, 'wa' => -12838, 'wai' => -12831, 'wan' => -12829, 'wang' => -12812, 'wei' => -12802, 'wen' => -12607, 'weng' => -12597, 'wo' => -12594, 'wu' => -12585, 'xi' => -12556, 'xia' => -12359, 'xian' => -12346, 'xiang' => -12320, 'xiao' => -12300, 'xie' => -12120, 'xin' => -12099, 'xing' => -12089, 'xiong' => -12074, 'xiu' => -12067, 'xu' => -12058, 'xuan' => -12039, 'xue' => -11867, 'xun' => -11861, 'ya' => -11847, 'yan' => -11831, 'yang' => -11798, 'yao' => -11781, 'ye' => -11604, 'yi' => -11589, 'yin' => -11536, 'ying' => -11358, 'yo' => -11340, 'yong' => -11339, 'you' => -11324, 'yu' => -11303, 'yuan' => -11097, 'yue' => -11077, 'yun' => -11067, 'za' => -11055, 'zai' => -11052, 'zan' => -11045, 'zang' => -11041, 'zao' => -11038, 'ze' => -11024, 'zei' => -11020, 'zen' => -11019, 'zeng' => -11018, 'zha' => -11014, 'zhai' => -10838, 'zhan' => -10832, 'zhang' => -10815, 'zhao' => -10800, 'zhe' => -10790, 'zhen' => -10780, 'zheng' => -10764, 'zhi' => -10587, 'zhong' => -10544, 'zhou' => -10533, 'zhu' => -10519, 'zhua' => -10331, 'zhuai' => -10329, 'zhuan' => -10328, 'zhuang' => -10322, 'zhui' => -10315, 'zhun' => -10309, 'zhuo' => -10307, 'zi' => -10296, 'zong' => -10281, 'zou' => -10274, 'zu' => -10270, 'zuan' => -10262, 'zui' => -10260, 'zun' => -10256, 'zuo' => -10254];

    /**
     * 字符串转拼音
     *
     * @param string $text
     * @param boolean $returnArray 是否拆分返回数组，默认 false
     *
     * @return mixed
     */
    public static function toPinyin($text, $returnArray = false)
    {
        $text = Charset::convertTo($text, 'GB2312');
        $result = [];
        $length = strlen($text);
        for ($i = 0; $i < $length; $i++) {
            $p = ord(substr($text, $i, 1));
            if ($p > 160) {
                $p = $p * 256 + ord(substr($text, ++$i, 1)) - 65536;
            }
            if ($p > 0 && $p < 160) {
                $result[] = chr($p);
            } elseif ($p < -20319 || $p > -10247) {
                return '';
            } else {
                $res = '';
                foreach (self::$__pinyin as $pin => $code) {
                    if ($code > $p) {
                        break;
                    }
                    $res = $pin;
                }
                $result[] = $res;
            }
        }
        return true === $returnArray ? $result : implode('', $result);
    }

    /**
     * 字符串转拼音首字母
     *
     * @param string $text
     * @param boolean $returnArray 是否拆分返回数组，默认 false
     *
     * @return mixed
     */
    public static function toPinyinFirst($text, $returnArray = false)
    {
        $array = self::toPinyin($text, true);
        $result = array_map(function ($row) {
            return self::sub($row, 0, 1);
        }, $array);
        return true === $returnArray ? $result : implode('', $result);
    }

    /**
     * 隐藏部分文字
     *
     * - 只支持三种模式，例如：3?4、?3、3?，数字代表显示的字符数，默认模式为：3?4
     *
     * @param string $string
     * @param string $hideChar 被替换的字符，默认为：****
     * @param string $mode 替换模式，默认为：3?4，即保留前 3 字符，后 4 字符，隐藏中间
     *
     * @return string
     */
    public static function hide($string, $hideChar = '****', $mode = '3?4')
    {
        $length = self::length($string);
        $modeArray = self::split($mode);
        $modeCount = Arrays::count($modeArray);
        if (1 !== Arrays::count($modeArray, '?')) {
            throw new Exception("模式错误，只允许有且仅有一个 ? 符，例如：3?4");
        }
        if ($length <= array_sum($modeArray)) {
            return $string;
        }
        if (3 === Arrays::count($modeArray)) {
            if ('?' === $modeArray[1]) {
                return self::sub($string, 0, $modeArray[0]) . $hideChar . self::sub($string, $length - $modeArray[2], $modeArray[2]);
            } else {
                throw new Exception("模式错误，三段时，? 符必须在中间，例如：3?4");
            }
        } elseif (2 === $modeCount) {
            if ('?' === $modeArray[0]) {
                return $hideChar . self::sub($string, $length - $modeArray[1], $modeArray[1]);
            } else {
                return self::sub($string, 0, $modeArray[0]) . $hideChar;
            }
        } else {
            throw new Exception("支持模式有三种，例如：3?4、?3、3?");
        }
    }

    /**
     * 多次换行
     *
     * @param integer $num 换行次数
     *
     * @return string
     */
    public static function eol($num = 1)
    {
        return str_repeat(PHP_EOL, $num);
    }

    /**
     * 返回字符串占用行数
     *
     * @param string $string
     *
     * @return integer
     */
    public static function lineNumber($string)
    {
        $array = explode(PHP_EOL, $string);
        return Arrays::count($array);
    }
}
