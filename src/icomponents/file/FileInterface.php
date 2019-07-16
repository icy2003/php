<?php
/**
 * interface FileInterface
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2019, icy2003
 */
namespace icy2003\php\icomponents\file;

/**
 * FileInterface
 */
interface FileInterface
{
    /**
     * 取得文件的上次访问时间
     *
     * @param string $fileName 文件的路径
     *
     * @return integer
     */
    public function getATime($fileName);

    /**
     * 返回路径中的文件名部分
     *
     * @param string $path 一个路径。无视 `/` 和 `\`
     * @param string $suffix 如果文件名是以 suffix 结束的，那这一部分也会被去掉
     *
     * @return string
     */
    public function getBasename($path, $suffix = null);

    /**
     * 取得文件的 inode 修改时间
     *
     * @param string $fileName 文件的路径
     *
     * @return integer
     */
    public function getCTime($fileName);

    /**
     * 获取文件后缀
     *
     * @param string $fileName 文件的路径
     *
     * @return string
     */
    public function getExtension($fileName);

    /**
     * 返回文件名（不带后缀）
     *
     * @param string $fileName 文件的路径
     *
     * @return string
     */
    public function getFilename($fileName);

    /**
     * 取得文件修改时间
     *
     * @param string $fileName 文件的路径
     *
     * @return integer
     */
    public function getMtime($fileName);

    /**
     * 返回路径中的目录部分
     *
     * @param string $path 一个路径。无视 `/` 和 `\`
     *
     * @return string
     */
    public function getDirname($path);

    /**
     * 获取目录或文件的权限
     *
     * @param string $path
     *
     * @return integer
     */
    public function getPerms($path);

    /**
     * 取得文件大小
     *
     * @param string $file 文件的路径
     *
     * @return integer
     */
    public function getFilesize($file);

    /**
     * 返回文件的类型
     *
     * - 可能的值有 fifo，char，dir，block，link，file 和 unknown
     *
     * @param string $path
     *
     * @return string
     */
    public function getType($path);

    /**
     * 是否是一个目录
     *
     * @param string $dir 目录的路径
     *
     * @return boolean
     */
    public function isDir($dir);

    /**
     * 判断是否是 . 和 ..
     *
     * @param string $dir 目录的路径
     *
     * @return boolean
     */
    public function isDot($dir);

    /**
     * 是否是一个文件
     *
     * @param string $file 文件的路径
     *
     * @return boolean
     */
    public function isFile($file);

    /**
     * 是否是一个链接
     *
     * @param string $link
     *
     * @return boolean
     */
    public function isLink($link);

    /**
     * 目录或文件是否可读
     *
     * @param string $path
     *
     * @return boolean
     */
    public function isReadable($path);

    /**
     * 目录或文件是否可写
     *
     * @param string $path
     *
     * @return boolean
     */
    public function isWritable($path);
}
