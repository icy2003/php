<?php
/**
 * Class SensitiveWord
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

/**
 * 敏感词过滤
 */
class SensitiveWord
{

    /**
     * 敏感词树
     *
     * @var array
     */
    protected $_map = [];

    /**
     * 添加一个敏感词
     *
     * @param string|array $content
     *
     * @return static
     */
    public function add($content)
    {
        $words = Strings::split($content);
        $temp = &$this->_map;
        foreach ($words as $word) {
            if (false === isset($temp[$word])) {
                $temp[$word] = [];
            }
            $temp = &$temp[$word];
        }
        $temp['isFind'] = true;
        return $this;
    }

    /**
     * 删除一个敏感词
     *
     * @param string|array $content
     *
     * @return static
     */
    public function del($content)
    {
        $words = is_string($content) ? Strings::split($content) : $content;
        $temp = &$this->_map;
        foreach ($words as $word) {
            if (isset($temp[$word])) {
                $temp = &$temp[$word];
            }
        }
        $temp['isFind'] = false;
        return $this;
    }

    /**
     * 从头开始检测一个字符串是否是敏感词
     *
     * - 发现敏感词，则返回该词
     * - 没有敏感词，则返回 false
     *
     * @param string|array $content
     * @param boolean $findMore
     *
     * @return string|boolean
     */
    protected function _isStart($content, $findMore = true)
    {
        $words = is_string($content) ? Strings::split($content) : $content;
        $temp = &$this->_map;
        $string = '';
        $isFind = false;
        foreach ($words as $word) {
            if (isset($temp[$word])) {
                $string .= $word;
                if (true === I::get($temp[$word], 'isFind')) {
                    $isFind = true;
                    if (true === $findMore) {
                        $temp = &$temp[$word];
                        continue;
                    } else {
                        return $string;
                    }
                } else {
                    $isFind = false;
                    $temp = &$temp[$word];
                    continue;
                }
            }
            return true === $isFind ? $string : false;
        }
    }

    /**
     * 查找一段文本里所有敏感词
     *
     * @param string|array $content
     *
     * @return array
     */
    public function find($content)
    {
        $words = is_string($content) ? Strings::split($content) : $content;
        $strings = [];
        while (true) {
            if (empty($words)) {
                return $strings;
            }
            $string = $this->_isStart($words);
            if (is_string($string)) {
                $strings[] = $string;
            }
            array_shift($words);
        }
    }

    /**
     * 返回替换敏感词后的文本
     *
     * @param string|array $content
     * @param string $char
     *
     * @return string
     */
    public function replace($content, $char = '*')
    {
        $words = $this->find($content);
        $content = implode('', $words);
        $array = [];
        foreach ($words as $word) {
            $array[$word] = Strings::repeat($char, Strings::length($word));
        }
        return Strings::replace($content, $array);
    }

    /**
     * 返回敏感词树
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_map;
    }

    /**
     * 导出敏感词树到 php 文件
     *
     * @param string|null $file
     *
     * @return void
     */
    public function toPhp($file = null)
    {
        null === $file && $file = __DIR__ . '/' . date('YmdHis') . '.php';
        $local = new LocalFile();
        $array = Arrays::export($this->toArray(), true);
        $local->putFileContent($file, <<<EOT
<?php

return {$array};

EOT);
    }
}
