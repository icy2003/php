<?php
/**
 * Class Console
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

use Symfony\Component\Process\Process;

/**
 * 控制台类
 *
 * 文字输出和命令执行
 */
class Console
{

    /**
     * 字体颜色控制码：黑色
     */
    const FG_BLACK = 30;
    /**
     * 字体颜色控制码：红色
     */
    const FG_RED = 31;
    /**
     * 字体颜色控制码：绿色
     */
    const FG_GREEN = 32;
    /**
     * 字体颜色控制码：黄色
     */
    const FG_YELLOW = 33;
    /**
     * 字体颜色控制码：蓝色
     */
    const FG_BLUE = 34;
    /**
     * 字体颜色控制码：紫色
     */
    const FG_PURPLE = 35;
    /**
     * 字体颜色控制码：青色
     */
    const FG_CYAN = 36;
    /**
     * 字体颜色控制码：灰色
     */
    const FG_GREY = 37;
    /**
     * 背景色控制码：黑色
     */
    const BG_BLACK = 40;
    /**
     * 背景色控制码：红色
     */
    const BG_RED = 41;
    /**
     * 背景色控制码：绿色
     */
    const BG_GREEN = 42;
    /**
     * 背景色控制码：黄色
     */
    const BG_YELLOW = 43;
    /**
     * 背景色控制码：蓝色
     */
    const BG_BLUE = 44;
    /**
     * 背景色控制码：紫色
     */
    const BG_PURPLE = 45;
    /**
     * 背景色控制码：青色
     */
    const BG_CYAN = 46;
    /**
     * 背景色控制码：灰色
     */
    const BG_GREY = 47;
    /**
     * 字体样式控制码：重置
     */
    const RESET = 0;
    /**
     * 字体样式控制码：普通
     */
    const NORMAL = 0;
    /**
     * 字体样式控制码：加粗
     */
    const BOLD = 1;
    /**
     * 字体样式控制码：斜体
     */
    const ITALIC = 3;
    /**
     * 字体样式控制码：下划线
     */
    const UNDERLINE = 4;
    /**
     * 字体样式控制码：闪烁
     */
    const BLINK = 5;
    /**
     * 字体样式控制码：
     */
    const NEGATIVE = 7;
    /**
     * 字体样式控制码：隐藏
     */
    const CONCEALED = 8;
    /**
     * 字体样式控制码：交叉输出
     */
    const CROSSED_OUT = 9;
    /**
     * 字体样式控制码：边框
     */
    const FRAMED = 51;
    /**
     * 字体样式控制码：环绕
     */
    const ENCIRCLED = 52;
    /**
     * 字体样式控制码：
     */
    const OVERLINED = 53;

    /**
     * 标准命令行输出
     *
     * @param string $string 输出文字
     *
     * @return integer|false
     */
    public static function stdout($string)
    {
        return fwrite(\STDOUT, $string);
    }

    /**
     * 标准命令行输入
     *
     * @return string
     */
    public static function stdin()
    {
        return rtrim((string) fgets(\STDIN), PHP_EOL);
    }

    /**
     * 标准命令行错误输出
     *
     * @param string $string 错误文字
     *
     * @return integer|false
     */
    public static function stderr($string)
    {
        return fwrite(\STDERR, $string);
    }

    /**
     * 输入提示
     *
     * @param string $prompt 输入提示
     * @param mixed $defaultValue 默认值
     *
     * @return string
     */
    public static function input($prompt = null, $defaultValue = '')
    {
        if (isset($prompt)) {
            self::output($prompt);
        }

        $input = self::stdin();
        if ('' === $input) {
            return (string) $defaultValue;
        }
        return $input;
    }

    /**
     * 输出提示
     *
     * @param string $string 提示文字
     *
     * @return integer|false
     */
    public static function output($string = null, $format = [])
    {
        if (!empty($format)) {
            $string = self::ansiFormat($string, $format);
        }
        return self::stdout($string . PHP_EOL);
    }

    /**
     * 输出列表
     *
     * @param array $array
     *
     * @return void
     */
    public static function outputList($array)
    {
        foreach($array as $string){
            self::output($string);
        }
    }

    /**
     * ANSI 格式化
     *
     * @param string $string ANSI 格式文本
     * @param array $format 格式代码数组
     *
     * @return string
     */
    public static function ansiFormat($string, $format = [])
    {
        $code = implode(';', $format);

        return "\033[0m" . ($code !== '' ? "\033[" . $code . 'm' : '') . $string . "\033[0m";
    }

    /**
     * 向终端发送 ANSI 控制代码 CUU，上移光标 n 行
     *
     * 如果光标已经在屏幕边缘，则此操作无效
     *
     * @param integer $rows 光标应向上移动的行数
     *
     * @return void
     */
    public static function moveCursorUp($rows = 1)
    {
        echo '\033[' . (int) $rows . 'A';
    }

    /**
     * 通过向终端发送 ANSI 控制代码 CUD，向下移动终端光标
     *
     * 如果光标已经在屏幕边缘，则此操作无效
     *
     * @param integer $rows 光标应向下移动的行数
     *
     * @return void
     */
    public static function moveCursorDown($rows = 1)
    {
        echo '\033[' . (int) $rows . 'B';
    }

    /**
     * 通过向终端发送 ANSI 控制代码 CUF，向前移动终端光标
     *
     * 如果光标已经在屏幕边缘，则此操作无效
     *
     * @param integer $steps 光标应向前移动的步数
     *
     * @return void
     */
    public static function moveCursorForward($steps = 1)
    {
        echo '\033[' . (int) $steps . 'C';
    }

    /**
     * 通过向终端发送 ANSI 控制代码 CUB，向后移动终端光标
     *
     * 如果光标已经在屏幕边缘，则此操作无效
     *
     * @param integer $steps 光标应向后移动的步数
     *
     * @return void
     */
    public static function moveCursorBackward($steps = 1)
    {
        echo '\033[' . (int) $steps . 'D';
    }

    /**
     * 向终端发送 ANSI 控制代码 CNL，让光标移到下 n 行的开头
     *
     * @param integer $lines 光标应向下移动的行数
     *
     * @return void
     */
    public static function moveCursorNextLine($lines = 1)
    {
        echo '\033[' . (int) $lines . 'E';
    }

    /**
     * 向终端发送 ANSI 控制代码 CPL，让光标移到上 n 行的开头
     *
     * @param integer $lines 光标应向上移动的行数
     *
     * @return void
     */
    public static function moveCursorPrevLine($lines = 1)
    {
        echo '\033[' . (int) $lines . 'F';
    }

    /**
     * 通过向终端发送 ANSI 控制代码 CUP 或 CPA，将光标移动到给定行和列的绝对位置上
     *
     * @param int $column 基于 1 的列号，1 是屏幕的左边缘
     * @param int $row 行 基于 1 的行数，1 是屏幕的上边缘。如果未设置，将只在当前行中移动光标
     *
     * @return void
     */
    public static function moveCursorTo($column, $row = null)
    {
        if ($row === null) {
            echo '\033[' . (int) $column . 'G';
        } else {
            echo '\033[' . (int) $row . ';' . (int) $column . 'H';
        }
    }

    /**
     * 通过向终端发送 ANSI 控制代码 SU 来向上滚动整页
     *
     * 在底部添加新行。在 Windows 中使用的 ANSI.SYS 不支持此操作
     *
     * @param integer $lines 要向上滚动的行数
     *
     * @return void
     */
    public static function scrollUp($lines = 1)
    {
        echo '\033[' . (int) $lines . 'S';
    }

    /**
     * 通过向终端发送 ANSI 控制代码 SD，向下滚动整页
     *
     * 在顶部添加新行。在 Windows 中使用的 ANSI.SYS 不支持此操作
     *
     * @param integer $lines 要向下滚动的行数
     *
     * @return void
     */
    public static function scrollDown($lines = 1)
    {
        echo '\033[' . (int) $lines . 'T';
    }

    /**
     * 通过向终端发送 ANSI 控制代码 SCP 来保存当前光标位置
     *
     * 然后可以使用 RestoreCursorPosition 恢复位置
     *
     * @return void
     */
    public static function saveCursorPosition()
    {
        echo '\033[s';
    }

    /**
     * 通过向终端发送 ANSI 控制代码 RCP，恢复用 SaveCursorPosition 保存的光标位置
     *
     * @return void
     */
    public static function restoreCursorPosition()
    {
        echo '\033[u';
    }

    /**
     * 通过发送 ANSI DECTCEM 代码隐藏光标到终端
     *
     * 使用 ShowCursor 将其带回
     *
     * 当应用程序退出时，不要忘记显示光标。退出后光标可能还隐藏在终端中
     *
     * @return void
     */
    public static function hideCursor()
    {
        echo '\033[?25l';
    }

    /**
     * 当被光标被 hideCursor 隐藏时，通过发送 ANSI DECTCEM 代码将光标显示到终端
     *
     * @return void
     */
    public static function showCursor()
    {
        echo '\033[?25h';
    }

    /**
     * 通过向终端发送带参数 2 的 ANSI 控制代码 ED，清除整个屏幕内容
     *
     * 不会更改光标位置
     *
     * 注意：在 Windows 中使用的 ANSI.SYS 实现将光标位置重置为屏幕的左上角
     *
     * @return void
     */
    public static function clearScreen()
    {
        echo '\033[2J';
    }

    /**
     * 通过将带参数 1 的ANSI控制代码 ED 发送到终端，清除从光标到屏幕开头的文本
     *
     * 不会更改光标位置
     *
     * @return void
     */
    public static function clearScreenBeforeCursor()
    {
        echo '\033[1J';
    }

    /**
     * 通过将带参数 0 的 ANSI 控制代码 ED 发送到终端，清除从光标到屏幕结尾的文本
     *
     * 不会更改光标位置
     *
     * @return void
     */
    public static function clearScreenAfterCursor()
    {
        echo '\033[0J';
    }

    /**
     * 清除行，通过向终端发送带参数 2 的 ANSI 控制代码 EL，光标当前处于打开状态
     *
     * 不会更改光标位置
     *
     * @return void
     */
    public static function clearLine()
    {
        echo '\033[2K';
    }

    /**
     * 通过将带参数 1 的 ANSI 控制代码 EL 发送到终端，清除从光标位置到行首的文本
     *
     * 不会更改光标位置
     *
     * @return void
     */
    public static function clearLineBeforeCursor()
    {
        echo '\033[1K';
    }

    /**
     * 通过将参数为 0 的 ANSI 控制代码 EL 发送到终端，清除从光标位置到行尾的文本
     *
     * 不会更改光标位置
     *
     * @return void
     */
    public static function clearLineAfterCursor()
    {
        echo '\033[0K';
    }

    /**
     * 从字符串中删除 ANSI 控制代码
     *
     * @param string $string 待处理的字符串
     *
     * @return string
     */
    public static function stripAnsiFormat($string)
    {
        return preg_replace('/\033\[[\d;?]*\w/', '', $string);
    }

    /**
     * 返回不带 ANSI 颜色代码的字符串的长度
     *
     * @param string $string 待测量的字符串
     *
     * @return int
     */
    public static function ansiStrlen($string)
    {
        return Strings::length(static::stripAnsiFormat($string));
    }

    /**
     * CLI 下获取命令参数
     *
     * @return array
     */
    public static function get()
    {
        global $argv;
        return array_slice($argv, 1);
    }

    /**
     * 执行一个命令并输出结果
     *
     * @param string $command
     *
     * @return string|false
     */
    public static function exec($command)
    {
        $process = new Process($command);
        $process->run();
        if (false === $process->isSuccessful()) {
            return false;
        }
        return $process->getOutput();
    }
}
