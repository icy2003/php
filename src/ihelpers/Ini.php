<?php
/**
 * Class Ini
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

use icy2003\php\C;
use icy2003\php\I;
use icy2003\php\icomponents\file\LocalFile;

/**
 * 获取配置数组
 * - 支持后缀格式：ini、json、xml
 * - ini 规则：
 *      1. 所有行会经过两边去白处理
 *      2. 英文分号（;），井号（#）开头的会被认为是注释，直接被舍弃
 *      3. 配置为键值对形式：[key]=[value]，等号两边空白会被舍弃
 *      4. 整个值如果被中括号（[]）包裹，则值会被英文逗号（,）分割为数组
 *      4. 如果一整行被中括号（[]）包裹，则该行被认为是个组，组名是中括号里的字符串
 *      5. 分组下的配置如果出现空白行，则重新分组
 *      6. 同名配置后者覆盖前者
 */
class Ini
{
    /**
     * 自动判断
     */
    const TYPE_AUTO = 'auto';
    /**
     * 自定义 ini 配置
     */
    const TYPE_INI = 'ini';
    /**
     * JSON 配置
     */
    const TYPE_JSON = 'json';
    /**
     * XML 配置
     */
    const TYPE_XML = 'xml';
    /**
     * 配置类型
     *
     * @var string
     */
    protected $_type;
    /**
     * 文件路径
     *
     * @var string
     */
    protected $_file;

    /**
     * 构造函数
     *
     * @param string $file 文件路径或者别名
     * @param string $type
     */
    public function __construct($file, $type = self::TYPE_AUTO)
    {
        $this->_file = $file;
        if (self::TYPE_AUTO === $type) {
            $local = new LocalFile();
            $extension = $local->getExtension($file);
            switch ($extension) {
                case 'json':
                    $this->_type = self::TYPE_JSON;
                    break;
                case 'xml':
                    $this->_type = self::TYPE_XML;
                    break;
                default:
                    $this->_type = self::TYPE_INI;
            }
        } else {
            C::assertTrue(in_array($type, ['json', 'xml', 'ini']), '不支持的后缀格式');
            $this->_type = $type;
        }
    }

    /**
     * 转成数组
     *
     * @return array
     */
    public function toArray()
    {
        $local = new LocalFile();
        $return = [];
        if (self::TYPE_INI === $this->_type) {
            $group = null;
            foreach ($local->linesGenerator($this->_file, true) as $line) {
                $array = [];
                $line = trim($line);
                // 如果 # 开头认为这是注释
                if (in_array(Strings::sub($line, 0, 1), ['#', ';'])) {
                    continue;
                }
                if (Strings::isVariable($line, ['[', ']'])) {
                    $group = Strings::sub($line, 1, -1);
                    continue;
                }
                if ('' === $line) {
                    $group = null;
                    continue;
                }
                list($name, $value) = Arrays::lists(explode('=', $line), 2, function ($row) {
                    return trim($row);
                });
                $array[$name] = $value;
                // 如果值被 [] 包括，则以英文逗号为分割，将值变成数组
                if (Strings::isStartsWith($value, '[') && Strings::isEndsWith($value, ']')) {
                    $array[$name] = explode(',', Strings::sub($value, 1, -1));
                }
                if (null === $group) {
                    $return = Arrays::merge($return, $array);
                } else {
                    $return[$group] = Arrays::merge((array) I::get($return, $group, []), $array);
                }
            }
        } elseif (self::TYPE_JSON === $this->_type) {
            $content = $local->getFileContent($this->_file);
            $return = Json::decode((string) $content);
        } elseif (self::TYPE_XML === $this->_type) {
            $content = $local->getFileContent($this->_file);
            $return = Xml::toArray((string) $content);
        }
        return $return;
    }
}
