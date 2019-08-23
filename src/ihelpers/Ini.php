<?php
/**
 * Class Ini
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

use icy2003\php\icomponents\file\LocalFile;

/**
 * 获取配置数组
 * - 支持三种配置文件：自定义、JSON、XML，后两个不必说，自定义的格式为：
 *      [配置名] = [配置值]，一行一个配置，结果将去掉左右两边空白
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
        $array = [];
        if (self::TYPE_INI === $this->_type) {
            foreach ($local->linesGenerator($this->_file, true) as $line) {
                // 如果 # 开头认为这是注释
                if (Strings::isStartsWith(trim($line), '#')) {
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
            }
        } elseif (self::TYPE_JSON === $this->_type) {
            $content = $local->getFileContent($this->_file);
            $array = Json::decode((string) $content);
        } elseif (self::TYPE_XML === $this->_type) {
            $content = $local->getFileContent($this->_file);
            $array = Xml::toArray((string) $content);
        }
        return $array;
    }
}
