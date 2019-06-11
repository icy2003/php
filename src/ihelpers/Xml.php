<?php
/**
 * Class Xml
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

use icy2003\php\I;

/**
 * Xml 类
 */
class Xml
{

    /**
     * 判断字符串是否是 Xml
     *
     * @param string $xmlString
     *
     * @return bool
     */
    public static function isXml($xmlString)
    {
        return false !== simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOERROR);
    }

    /**
     * Xml 转成数组
     *
     * 失败时返回空数组
     *
     * @param string $xmlString Xml 字符串
     *
     * @return array
     */
    public static function toArray($xmlString)
    {
        if (false === self::isXml($xmlString)) {
            return [];
        }
        $isDisabled = libxml_disable_entity_loader(true);
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
        $array = Json::decode(Json::encode($xml), true);
        libxml_disable_entity_loader($isDisabled);
        return $array;
    }

    /**
     * 数组转成 Xml 字符串
     *
     * @param array $array 数组
     * @param bool $isRoot 是否带上根节点，默认 true
     *
     * @return string
     */
    public static function fromArray($array, $isRoot = true)
    {
        $xmlstring = '';
        true === $isRoot && $xmlstring .= '<xml>';
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $subXmlString = self::fromArray($value, false);
                $xmlstring .= '<' . $key . '>' . $subXmlString . '</' . $key . '>';
            } else {
                if (is_numeric($value)) {
                    $xmlstring .= '<' . $key . '>' . $value . '</' . $key . '>';
                } else {
                    $xmlstring .= '<' . $key . '><![CDATA[' . $value . ']]></' . $key . '>';
                }
            }
        }
        true === $isRoot && $xmlstring .= '</xml>';
        return $xmlstring;
    }

    /**
     * 获取 Xml 字符串里的参数
     *
     * @param string $xmlString Xml 字符串
     * @param string $key @see \icy2003\php\I::get
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public static function value($xmlString, $key, $defaultValue = null)
    {
        $array = self::toArray($xmlString);
        return I::get($array, $key, $defaultValue);
    }
}
