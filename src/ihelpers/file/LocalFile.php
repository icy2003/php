<?php
/**
 * Class LocalFile
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2019, icy2003
 */
namespace icy2003\php\ihelpers\file;

use icy2003\php\ihelpers\File;

/**
 * 本地文件
 */
class LocalFile extends Base
{
    /**
     * 是否是一个文件
     *
     * @param string $file 文件的路径
     *
     * @return boolean
     */
    public function getIsFile($file)
    {
        return File::fileExists($file);
    }

    /**
     * 是否是一个目录
     *
     * @param string $dir 目录的路径
     *
     * @return boolean
     */
    public function getIsDir($dir)
    {
        return is_dir($dir);
    }

    /**
     * 列出指定路径中的文件和目录
     *
     * 返回文件和目录的全路径
     *
     * @param string $dir 目录的路径
     *
     * @return array
     */
    public function getLists($dir = null)
    {
        null === $dir && $dir = './';
        return array_map(function ($file) use ($dir) {
            return rtrim($dir, '/') . '/' . $file;
        }, array_diff(scandir($dir), ['..', '.']));
    }

    /**
     * 取得文件大小
     *
     * @param string $file 文件的路径
     *
     * @return integer
     */
    public function getFilesize($file)
    {
        return filesize($file);
    }

    /**
     * 将整个文件读入一个字符串
     *
     * @param string $file 要读取的文件的名称
     *
     * @return string
     */
    public function getFileContent($file)
    {
        return file_get_contents($file);
    }

    /**
     * 创建文件（目录会被递归地创建）
     *
     * @param string $file 文件的路径
     * @param integer $mode 默认的 mode 是 0777，意味着最大可能的访问权
     *
     * @return boolean
     */
    public function createFile($file, $mode = 0777)
    {
        return File::createFile($file, $mode);
    }

    /**
     * 递归地创建目录
     *
     * @param string $dir 目录的路径
     * @param integer $mode 默认的 mode 是 0777，意味着最大可能的访问权
     *
     * @return boolean
     */
    public function createDir($dir, $mode = 0777)
    {
        return File::createDir($dir, $mode);
    }

    /**
     * 删除一个文件
     *
     * @param string $file 文件的路径
     *
     * @return boolean
     */
    public function deleteFile($file)
    {
        return File::deleteFile($file);
    }

    /**
     * 递归地删除目录
     *
     * @param string $dir 目录的路径
     * @param boolean $deleteRoot 是否删除目录的根节点，默认 true，即删除
     *
     * @return boolean
     */
    public function deleteDir($dir, $deleteRoot = true)
    {
        return File::deleteDir($dir, $deleteRoot);
    }

    /**
     * 复制文件（目录会被递归地创建）
     *
     * @param string $fromFile 文件原路径
     * @param string $toFile 文件目标路径
     * @param boolean $overWrite 已存在的文件是否被覆盖，默认 false，即不覆盖
     *
     * @return boolean
     */
    public function copyFile($fromFile, $toFile, $overWrite = false)
    {
        return File::copyFile($fromFile, $toFile, $overWrite);
    }

    /**
     * 递归地复制目录
     *
     * @param string $fromDir 目录原路径
     * @param string $toDir 目录目标路径
     * @param boolean $overWrite 已存在的目录是否被覆盖，默认 false，即不覆盖
     *
     * @return boolean
     */
    public function copyDir($fromDir, $toDir, $overWrite = false)
    {
        return File::copyDir($fromDir, $toDir, $overWrite);
    }

    /**
     * 移动文件（目录会被递归地创建）
     *
     * @param string $fromFile 文件原路径
     * @param string $toFile 文件目标路径
     * @param boolean $overWrite 已存在的文件是否被覆盖，默认 false，即不覆盖
     *
     * @return boolean
     */
    public function moveFile($fromFile, $toFile, $overWrite = false)
    {
        return File::moveFile($fromFile, $toFile, $overWrite);
    }

    /**
     * 递归地移动目录
     *
     * @param string $fromDir 目录原路径
     * @param string $toDir 目录目标路径
     * @param boolean $overWrite 已存在的目录是否被覆盖，默认 false，即不覆盖
     *
     * @return boolean
     */
    public function moveDir($fromDir, $toDir, $overWrite = false)
    {
        return File::moveDir($fromDir, $toDir, $overWrite);
    }

}
