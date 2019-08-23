<?php
/**
 * Class Markdown
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

/**
 * Markdown 类
 */
class Markdown
{

    /**
     * 预处理文本内容（html 转义、去除两边空白）
     *
     * @param string $text
     *
     * @return string
     */
    private static function __text($text)
    {
        return Html::encode(trim($text));
    }

    /**
     * 标题
     *
     * @param string $text 标题内容
     * @param int $level 标题层级，默认1，一级标题
     *
     * @return string
     */
    public static function title($text, $level = 1)
    {
        return str_repeat('#', $level) . ' ' . self::__text($text);
    }

    /**
     * 字体加粗
     *
     * @param string $text 文本内容
     *
     * @return string
     */
    public static function bold($text)
    {
        return '**' . self::__text($text) . '**';
    }

    /**
     * 斜体
     *
     * @param string $text 文本内容
     *
     * @return string
     */
    public static function italics($text)
    {
        return '*' . self::__text($text) . '*';
    }

    /**
     * a 链接
     *
     * @todo 兼容标准语法
     *
     * @param string $text 链接文字
     * @param string $url 链接地址
     * @param string $title 链接标题
     * @param bool $openInNew 是否在新窗口打开
     *
     * @return string
     */
    public static function a($text, $url, $title = null, $openInNew = false)
    {
        return '[' . self::__text($text) . '](' . $url . ' "' . (null === $title ? $text : $title) . '"' . ')' . (true === $openInNew ? '{:target="_blank"}' : '');
    }

    /**
     * 删除线
     *
     * @param string $text 文本内容
     *
     * @return string
     */
    public static function strikethrough($text)
    {
        return '~~' . self::__text($text) . '~~';
    }

    /**
     * 引用
     *
     * @param string $text 文本内容
     *
     * @return string
     */
    public static function quote($text)
    {
        return '> ' . self::__text($text);
    }

    /**
     * 分隔符
     *
     * @return string
     */
    public static function separator()
    {
        return '---';
    }

    /**
     * 图片
     *
     * @param string $url 图片链接
     * @param string $title 图片标题
     * @param string $alt alt
     *
     * @return string
     */
    public static function image($url, $title = null, $alt = null)
    {
        return '![' . $alt . '](' . $url . ' "' . $title . '")';
    }

    /**
     * 无序列表
     *
     * @todo 支持多维数组
     *
     * @param array $array 列表数组
     *
     * @return string
     */
    public static function ul($array)
    {
        return implode(PHP_EOL, array_map(function ($row) {
            return '- ' . self::__text($row);
        }, $array));
    }

    /**
     * 有序列表
     *
     * @todo 支持多维数组
     *
     * @param array $array 列表数组
     *
     * @return string
     */
    public static function ol($array)
    {
        return implode(PHP_EOL, array_map(function ($row) {
            static $i = 1;
            return ($i++) . '. ' . self::__text($row);
        }, $array));
    }

    /**
     * 代码
     *
     * @param string $text 代码内容
     *
     * @return string
     */
    public static function code($text)
    {
        return '`' . self::__text($text) . '`';
    }

    /**
     * 多行代码
     *
     * @param string $text 多行代码内容
     * @param string $type 代码类型
     *
     * @return string
     */
    public static function codes($text, $type = null)
    {
        return '```' . $type . PHP_EOL . self::__text($text) . PHP_EOL . '```';
    }

    /**
     * 表格
     *
     * @todo 样式和反转
     *
     * @param array $array 表格数组，支持索引和关联数组，索引时要求为二维数组，第一行被认为是表头，关联时键被当成表头
     * @param array $styleArray 样式数组
     * @param bool $transpose 是否反转
     *
     * @return string
     */
    public static function table($array, /** @scrutinizer ignore-unused */$styleArray = [], /** @scrutinizer ignore-unused */$transpose = false)
    {
        if (empty($array)) {
            return '';
        }
        $isAssoc = Arrays::isAssoc($array);
        if (true === $isAssoc) {
            $title = array_keys($array);
            $rows = array_values($array);
        } else {
            $title = array_shift($array);
            $rows = $array;
        }
        $lineFunc = function ($arr) {
            return '|' . implode('|', array_map(function ($line) {
                return ' ' . self::__text($line) . ' ';
            }, $arr)) . '|';
        };
        $string = '';
        $string .= $lineFunc($title) . PHP_EOL;
        $string .= $lineFunc(Arrays::fill(0, count($title), ':-:')) . PHP_EOL;
        foreach ($rows as $row) {
            $string .= $lineFunc($row) . PHP_EOL;
        }
        return rtrim($string);
    }

    /**
     * 待办事项行
     *
     * @param string $line 任务行
     * @param bool $isDone 是否完成
     * @param int $level 层级，默认1
     *
     * @return string
     */
    public static function todo($line, $isDone, $level = 1)
    {
        return str_repeat(' ', $level - 1) . '- [' . (true === $isDone ? 'x' : ' ') . '] ' . $line;
    }

    /**
     * 生成目录
     *
     * @return string
     */
    public static function toc()
    {
        return '[TOC]';
    }

    /**
     * markdown 文档开始
     *
     * @return void
     */
    public static function start()
    {
        ob_start();
        ob_implicit_flush(0);
    }

    /**
     * markdown 文档结束并转成字符串
     *
     * @return string
     */
    public static function toString()
    {
        return ob_get_clean();
    }

    /**
     * markdown 文档结束并导出到文件
     *
     * @param string $file 文件路径
     *
     * @return void
     */
    public static function toFile($file = null)
    {
        $content = self::toString();
        null === $file && $file = './' . date('YmdHis') . '.md';
        file_put_contents($file, $content);
    }

}
