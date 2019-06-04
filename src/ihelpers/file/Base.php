<?php
/**
 * abstract Class Base
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers\file;

use Symfony\Component\Filesystem\Filesystem;

/**
 * 文件抽象类
 *
 * - getCommandResult：获得命令返回值
 * - getBasename：返回路径中的文件名部分
 * - getDirname：返回路径中的目录部分
 * - getIsFile：是否是一个文件
 * - getIsDir：是否是一个目录
 * - getIsAbsolute：是否是一个绝对路径
 * - getRealpath：返回规范化的绝对路径名
 * - getLists：列出指定路径中的文件和目录
 * - getFilesize：取得文件大小
 * - getFileContent：将整个文件读入一个字符串
 * - createFile：创建文件（目录会被递归地创建）
 * - createFileFromString：创建一个文件（目录会被递归地创建），并用字符串填充进文件
 * - createDir：递归地创建目录
 * - deleteFile：删除一个文件
 * - deleteDir：递归地删除目录
 * - copyFile：复制文件（目录会被递归地创建）
 * - copyDir：递归地复制目录
 * - moveFile：移动文件（目录会被递归地创建）
 * - moveDir：递归地移动目录
 * - chown：改变文件（目录）的创建者
 * - chgrp：改变一个文件（目录）的群组
 * - chmod：改变文件（目录）的安全模式
 * - symlink：建立符号连接
 * - close：关闭文件句柄
 */
abstract class Base
{
    /**
     * 获得命令返回值
     *
     * @param string $command 命令
     *
     * @return string
     */
    abstract public function getCommandResult($command);

    /**
     * 返回路径中的文件名部分
     *
     * @param string $path 一个路径。无视 `/` 和 `\`
     * @param string $suffix 如果文件名是以 suffix 结束的，那这一部分也会被去掉
     *
     * @return string
     */
    public function getBasename($path, $suffix = null)
    {
        $path = str_replace('\\', '/', $path);
        return basename($path, $suffix);
    }

    /**
     * 返回路径中的目录部分
     *
     * @param string $path 一个路径。无视 `/` 和 `\`
     *
     * @return string
     */
    public function getDirname($path)
    {
        $path = str_replace('\\', '/', $path);
        return dirname($path);
    }

    /**
     * 是否是一个文件
     *
     * @param string $file 文件的路径
     *
     * @return boolean
     */
    abstract public function getIsFile($file);

    /**
     * 是否是一个目录
     *
     * @param string $dir 目录的路径
     *
     * @return boolean
     */
    abstract public function getIsDir($dir);

    /**
     * 是否是一个绝对路径
     *
     * @param string $file 文件或目录
     *
     * @return boolean
     */
    public function getIsAbsolute($file)
    {
        $fs = new Filesystem();
        return $fs->isAbsolutePath($file);
    }

    /**
     * 返回规范化的绝对路径名
     *
     * - 不支持 realpath 的类：FtpFile
     * - 这些类的函数实现是：处理输入的 path 中的 '/./', '/../' 以及多余的 '/'
     *
     * @param string $path 要检查的路径
     *
     * @return string
     */
    public function getRealpath($path)
    {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }

            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }

    /**
     * 列出指定路径中的文件和目录
     *
     * - 优先返回叶节点
     * - 不包含“.”和“..”
     *
     * @param string $dir 目录的路径，如不给，则使用当前路径（pwd）
     * @param integer $flags 选项设置，默认 FileConstants::COMPLETE_PATH，可支持参数：
     * - FileConstants::COMPLETE_PATH：使用完整路径
     * - FileConstants::COMPLETE_PATH_DISABLED：不使用完整路径
     * - FileConstants::RECURSIVE：递归遍历
     * - FileConstants::RECURSIVE_DISABLED：不递归
     *
     * @return array
     */
    abstract public function getLists($dir = null, $flags = FileConstants::COMPLETE_PATH);

    /**
     * 取得文件大小
     *
     * @param string $file 文件的路径
     *
     * @return integer
     */
    abstract public function getFilesize($file);

    /**
     * 将整个文件读入一个字符串
     *
     * @param string $file 要读取的文件的名称
     *
     * @return string
     */
    abstract public function getFileContent($file);

    /**
     * 创建文件（目录会被递归地创建）
     *
     * @param string $file 文件的路径
     * @param integer $mode 默认的 mode 是 0777，意味着最大可能的访问权
     *
     * @return boolean
     */
    abstract public function createFile($file, $mode = 0777);

    /**
     * 创建一个文件（目录会被递归地创建），并用字符串填充进文件
     *
     * @param string $file 文件的路径
     * @param string $string 待填充进文件的字符串
     * @param integer $mode 默认的 mode 是 0777，意味着最大可能的访问权
     *
     * @return boolean
     */
    abstract public function createFileFromString($file, $string, $mode = 0777);

    /**
     * 递归地创建目录
     *
     * @param string $dir 目录的路径
     * @param integer $mode 默认的 mode 是 0777，意味着最大可能的访问权
     *
     * @return boolean
     */
    abstract public function createDir($dir, $mode = 0777);

    /**
     * 删除一个文件
     *
     * @param string $file 文件的路径
     *
     * @return boolean
     */
    abstract public function deleteFile($file);

    /**
     * 递归地删除目录
     *
     * @param string $dir 目录的路径
     * @param boolean $deleteRoot 是否删除目录的根节点，默认 true，即删除
     *
     * @return boolean
     */
    abstract public function deleteDir($dir, $deleteRoot = true);

    /**
     * 复制文件（目录会被递归地创建）
     *
     * @param string $fromFile 文件原路径
     * @param string $toFile 文件目标路径
     * @param boolean $overWrite 已存在的文件是否被覆盖，默认 false，即不覆盖
     *
     * @return boolean
     */
    abstract public function copyFile($fromFile, $toFile, $overWrite = false);

    /**
     * 递归地复制目录
     *
     * @param string $fromDir 目录原路径
     * @param string $toDir 目录目标路径
     * @param boolean $overWrite 已存在的目录是否被覆盖，默认 false，即不覆盖
     *
     * @return boolean
     */
    abstract public function copyDir($fromDir, $toDir, $overWrite = false);

    /**
     * 移动文件（目录会被递归地创建）
     *
     * @param string $fromFile 文件原路径
     * @param string $toFile 文件目标路径
     * @param boolean $overWrite 已存在的文件是否被覆盖，默认 false，即不覆盖
     *
     * @return boolean
     */
    abstract public function moveFile($fromFile, $toFile, $overWrite = false);

    /**
     * 递归地移动目录
     *
     * @param string $fromDir 目录原路径
     * @param string $toDir 目录目标路径
     * @param boolean $overWrite 已存在的目录是否被覆盖，默认 false，即不覆盖
     *
     * @return boolean
     */
    abstract public function moveDir($fromDir, $toDir, $overWrite = false);

    /**
     * 改变文件（目录）的创建者
     *
     * @param string $file 文件或者目录
     * @param string $user 创建者
     * @param integer $flags 选项设置，默认 FileConstants::RECURSIVE_DISABLED，可支持参数：
     * - FileConstants::RECURSIVE_DISABLED
     * - FileConstants::RECURSIVE
     *
     * @return boolean
     */
    abstract public function chown($file, $user, $flags = FileConstants::RECURSIVE_DISABLED);

    /**
     * 改变文件（目录）的群组
     *
     * @param string $file 文件或者目录
     * @param string $group 群组
     * @param integer $flags 选项设置，默认 FileConstants::RECURSIVE_DISABLED，可支持参数：
     * - FileConstants::RECURSIVE_DISABLED
     * - FileConstants::RECURSIVE
     *
     * @return boolean
     */
    abstract public function chgrp($file, $group, $flags = FileConstants::RECURSIVE_DISABLED);

    /**
     * 改变文件（目录）的安全模式
     *
     * @param string $file 文件或者目录
     * @param integer $mode 默认的 mode 是 0777，意味着最大可能的访问权
     * @param integer $flags 选项设置，默认 FileConstants::RECURSIVE_DISABLED，可支持参数：
     * - FileConstants::RECURSIVE_DISABLED
     * - FileConstants::RECURSIVE
     * @return boolean
     */
    abstract public function chmod($file, $mode = 0777, $flags = FileConstants::RECURSIVE_DISABLED);

    /**
     * 建立符号连接
     *
     * @param string $from 连接的目标
     * @param string $to 连接的名称
     *
     * @return boolean
     */
    abstract public function symlink($from, $to);

    /**
     * 关闭文件句柄（连接）
     *
     * @return boolean
     */
    abstract public function close();

    /**
     * 析构函数：关闭文件句柄（连接）
     */
    public function __destruct()
    {
        $this->close();
    }
}
