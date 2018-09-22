<?php

namespace icy2003\ihelpers;

/**
 * 文件 IO
 * FileIO::createDir    创建一个目录（递归）
 * FileIO::createFile   创建一个文件（递归创建目录）
 * FileIO::deleteFile   删除一个文件
 * FileIO::deleteDir    删除一个目录（递归）
 * FileIO::copyFile     复制文件（递归创建目录）
 * FileIO::copyDir      复制目录（递归复制目录）
 * FileIO::moveFile     移动文件（递归创建目录）
 * FileIO::moveDir      移动目录（递归移动目录，强制删除旧目录）.
 */
class FileIO
{
    /**
     * 创建一个目录（递归）.
     *
     * @param string $dir 目录路径
     *
     * @return bool 是否成功创建
     */
    public static function createDir($dir)
    {
        return is_dir($dir) || self::createDir(dirname($dir)) && mkdir($dir, 0777);
    }

    /**
     * 创建一个文件（递归创建目录）.
     *
     * @param string $file 文件路径
     *
     * @return bool 是否成功创建
     */
    public static function createFile($file)
    {
        return file_exists($file) || self::createDir(dirname($file)) && touch($file);
    }

    /**
     * 删除一个文件.
     *
     * @param string $file 文件路径
     *
     * @return bool 是否成功删除
     */
    public static function deleteFile($file)
    {
        return file_exists($file) && unlink($file);
    }

    /**
     * 删除一个目录（递归）.
     *
     * @param string $dir 目录路径
     *
     * @return bool 是否成功删除
     */
    public static function deleteDir($dir)
    {
        // glob 函数拿不到隐藏文件
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            is_dir("{$dir}/{$file}") ? self::deleteDir("{$dir}/{$file}") : unlink("{$dir}/{$file}");
        }

        return rmdir($dir);
    }

    /**
     * 复制文件（递归创建目录）。
     *
     * @param string $fromFile  原文件路径
     * @param string $toFile    目标文件路径
     * @param bool   $overWrite 是否覆盖，默认 false
     *
     * @return bool 是否复制成功
     */
    public static function copyFile($fromFile, $toFile, $overWrite = false)
    {
        if (!file_exists($fromFile)) {
            return false;
        }
        if (file_exists($toFile)) {
            if (false === $overWrite) {
                return false;
            } else {
                self::deleteFile($toFile);
            }
        }
        self::createDir(dirname($toFile));
        copy($fromFile, $toFile);

        return true;
    }

    /**
     * 复制目录（递归复制目录）.
     *
     * @param string $fromDir   原目录路径
     * @param string $toDir     目标目录路径
     * @param bool   $overWrite 是否覆盖，默认 false
     *
     * @return bool 是否复制成功
     */
    public static function copyDir($fromDir, $toDir, $overWrite = false)
    {
        $fromDir = rtrim($fromDir, '/').'/';
        $toDir = rtrim($toDir, '/').'/';
        if (!is_dir($fromDir)) {
            return false;
        }
        if (false === ($dirHanlder = opendir($fromDir))) {
            return false;
        }
        self::createDir($toDir);
        while (false !== ($file = readdir($dirHanlder))) {
            if ('.' == $file || '..' == $file) {
                continue;
            }
            if (is_dir($fromDir.$file)) {
                self::copyDir($fromDir.$file, $toDir.$file, $overWrite);
            } else {
                self::copyFile($fromDir.$file, $toDir.$file, $overWrite);
            }
        }
        closedir($dirHanlder);

        return true;
    }

    /**
     * 移动文件（递归创建目录）.
     *
     * @param string $fromFile  原文件路径
     * @param string $toFile    目标文件路径
     * @param bool   $overWrite 是否覆盖，默认 false
     *
     * @return bool 是否移动成功
     */
    public static function moveFile($fromFile, $toFile, $overWrite = false)
    {
        if (!file_exists($fromFile)) {
            return false;
        }
        if (file_exists($toFile)) {
            if (false === $overWrite) {
                return false;
            } else {
                self::deleteFile($toFile);
            }
        }
        self::createDir(dirname($toFile));
        rename($fromFile, $toFile);

        return true;
    }

    /**
     * 移动目录（递归移动目录，强制删除旧目录）.
     *
     * @param string $fromDir   原目录路径
     * @param string $toDir     目标目录路径
     * @param bool   $overWrite 是否覆盖，默认 false
     *
     * @return bool 是否移动成功
     */
    public static function moveDir($fromDir, $toDir, $overWrite = false)
    {
        $fromDir = rtrim($fromDir, '/').'/';
        $toDir = rtrim($toDir, '/').'/';
        if (!is_dir($fromDir)) {
            return false;
        }
        if (false === ($dirHanlder = opendir($fromDir))) {
            return false;
        }
        self::createDir($toDir);
        while (false !== ($file = readdir($dirHanlder))) {
            if ('.' == $file || '..' == $file) {
                continue;
            }
            if (is_dir($fromDir.$file)) {
                self::moveDir($fromDir.$file, $toDir.$file, $overWrite);
            } else {
                self::moveFile($fromDir.$file, $toDir.$file, $overWrite);
            }
        }
        closedir($dirHanlder);

        return self::deleteDir($fromDir);
    }
}
