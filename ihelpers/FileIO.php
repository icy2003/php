<?php

namespace icy2003\ihelpers;

/**
 * 文件 IO
 * FileIO::createDir($dir)      创建一个目录（递归）
 * FileIO::createFile($file)    创建一个文件（递归创建目录）
 * FileIO::deleteFile($file)    删除一个文件
 * FileIO::deleteDir($dir)      删除一个目录（递归）
 * FileIO::moveFile($fromFile, $toFile, $overWrite = false) 移动文件（递归创建目录）
 * FileIO::moveDir($fromDir, $toDir, $overWrite = false) 移动目录（递归移动目录，强制删除旧目录）
 * FileIO::copyFile($fromFile, $toFile, $overWrite = false) 复制文件（递归创建目录）
 * FileIO::copyDir($fromDir, $toDir, $overWrite = false) 复制目录（递归复制目录）
 */
class FileIO
{
    public static function createDir($dir)
    {
        return is_dir($dir) || self::createDir(dirname($dir)) && mkdir($dir, 0777);
    }

    public static function createFile($file)
    {
        return file_exists($file) || self::createDir(dirname($file)) && touch($file);
    }

    public static function deleteFile($file)
    {
        return file_exists($file) && unlink($file);
    }

    public static function deleteDir($dir)
    {
        // glob 函数拿不到隐藏文件
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            is_dir("{$dir}/{$file}") ? self::deleteDir("{$dir}/{$file}") : unlink("{$dir}/{$file}");
        }

        return rmdir($dir);
    }

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
