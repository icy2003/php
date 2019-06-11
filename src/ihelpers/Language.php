<?php
/**
 * Class Language
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

use icy2003\php\I;

/**
 * 语言文件类
 */
class Language
{
    /**
     * 单例对象
     *
     * @var static
     */
    protected static $_instance;
    /**
     * 配置
     *
     * @var array
     */
    private $__config;

    /**
     * 分类
     *
     * @var array
     */
    private $__categories = [];

    /**
     * 构造函数
     *
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * 克隆函数
     *
     * @return void
     */
    private function __clone()
    {
    }
    /**
     * 创建一个语言单例
     *
     * @return static
     */
    public static function create()
    {
        if (!static::$_instance instanceof static ) {
            static::$_instance = new static();
            $config = include I::getAlias('@icy2003/php/config.php');
            static::$_instance->__config = I::get($config, 'Language');
        }

        return static::$_instance;
    }

    /**
     * 获得配置数组
     *
     * @return void
     */
    public function getConfig()
    {
        return $this->__config;
    }

    /**
     * 设置配置数组
     *
     * @param array $config
     *
     * @return void
     */
    public function setConfig($config)
    {
        $this->__config = $config;
    }

    /**
     * 加载配置
     *
     * @param array $config
     *
     * @return void
     */
    public function loadConfig($config)
    {
        $this->setConfig(Arrays::arrayMergeRecursive($this->__config, $config));
    }

    /**
     * 翻译
     *
     * @param string $category 分类
     * @param string $message 消息
     * @param array $params 参数
     * @param string $language 语言
     *
     * @return void
     */
    public function t($category, $message, $params = [], $language = 'zh-cn')
    {
        $config = $this->__config;
        $language = !empty($language) ? $language : I::get($config, 'language');
        $basePath = I::get($config, 'basePath');
        if (empty($this->__categories[$category . '_' . $language])) {
            $map = include I::getAlias(trim($basePath, '/') . '/' . $language . '/' . $category . '.php');
            $this->__categories[$category . '_' . $language] = $map;
        } else {
            $map = $this->__categories[$category . '_' . $language];
        }
        return str_replace(array_map(function ($data) {
            return '{' . $data . '}';
        }, array_keys($params)), array_values($params), I::get($map, $message, $message));
    }
}
