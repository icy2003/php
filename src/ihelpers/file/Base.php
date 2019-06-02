<?php
/**
 * abstract Class Base
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2019, icy2003
 */
namespace icy2003\php\ihelpers\file;

use icy2003\php\ihelpers\File;

/**
 * 文件抽象类
 */
abstract class Base
{

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
        return File::basename($path, $suffix);
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
        return File::dirname($path);
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
     * 列出指定路径中的文件和目录
     *
     * 返回文件和目录的全路径
     *
     * @param string $dir 目录的路径
     *
     * @return array
     */
    abstract public function getLists($dir = null);

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
}
