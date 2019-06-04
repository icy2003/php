<?php
/**
 * Class FtpFile
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2019, icy2003
 */
namespace icy2003\php\ihelpers\file;

use icy2003\php\I;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\File;

/**
 * FTP 文件
 */
class FtpFile extends Base
{

    /**
     * FTP 连接
     *
     * @var resource
     */
    protected $_conn;

    /**
     * 构造函数
     *
     * @param array $config ftp 连接配置。至少包括：host、username、password，可选：port、timeout
     */
    public function __construct($config)
    {
        if (!function_exists('ftp_connect')) {
            throw new \Exception("请开启 ftp 扩展");
        }
        if (!Arrays::arrayKeysExists(['host', 'username', 'password'], $config, $diff)) {
            throw new \Exception('缺少 ' . implode(',', $diff) . ' 参数');
        }
        $this->_conn = ftp_connect(I::value($config, 'host'), I::value($config, 'port', 21), I::value($config, 'timeout', 90));
        if (false === $this->_conn) {
            throw new \Exception("连接失败");
        }
        if (false === @ftp_login($this->_conn, I::value($config, 'username'), I::value($config, 'password'))) {
            throw new \Exception("账号密码错误");
        }
        ftp_pasv($this->_conn, true);
    }

    /**
     * @ignore
     */
    public function getCommandResult($command)
    {
        return implode(',', ftp_raw($this->_conn, $command));
    }

    /**
     * @ignore
     */
    public function getIsFile($file)
    {
        return ftp_size($this->_conn, $file) > -1;
    }

    /**
     * @ignore
     */
    public function getIsDir($dir)
    {
        return ($current = ftp_pwd($this->_conn)) &&
        (bool) @ftp_chdir($this->_conn, $dir) &&
        ftp_chdir($this->_conn, $current);
    }

    /**
     * @ignore
     */
    public function getLists($dir = null, $flags = FileConstants::COMPLETE_PATH)
    {
        static $list = [];
        null === $dir && $dir = ftp_pwd($this->_conn);
        $dir = rtrim($dir, '/') . '/';
        $files = ftp_nlist($this->_conn, $dir);
        foreach ($files as $file) {
            if (I::hasFlag($flags, FileConstants::RECURSIVE) && $this->getIsDir($file)) {
                $this->getLists($file, $flags);
            }
            $list[] = I::hasFlag($flags, FileConstants::COMPLETE_PATH) ? $file : $this->getBasename($file);
        }
        return $list;
    }

    /**
     * @ignore
     */
    public function getFilesize($file)
    {
        return ftp_size($this->_conn, $file);
    }

    /**
     * @ignore
     */
    public function getFileContent($file)
    {
        $fp = fopen('php://temp', 'r+');
        if (@ftp_fget($this->_conn, $fp, $file, FTP_BINARY, 0)) {
            rewind($fp);
            $content = stream_get_contents($fp);
            fclose($fp);
            return $content;
        } else {
            return false;
        }
    }

    /**
     * 创建文件（非递归的）
     *
     * @param string $file 文件的路径
     *
     * @return boolean
     */
    private function __createFile($file)
    {
        $fp = fopen("php://temp", 'r');
        $isNewFile = @ftp_fput($this->_conn, $file, $fp, FTP_BINARY);
        fclose($fp);
        return $isNewFile;
    }

    /**
     * @ignore
     */
    public function createFile($file, $mode = 0777)
    {
        if (false === $this->getIsFile($file)) {
            $this->createDir($this->getDirname($file), $mode);
            $this->__createFile($file);
        }
        return $this->chmod($file, $mode, FileConstants::RECURSIVE_DISABLED);
    }

    /**
     * @ignore
     */
    public function createFileFromString($file, $string, $mode = 0777)
    {
        $this->createDir($this->getDirname($file));
        $fp = fopen('data://text/plain,' . $string, 'r');
        $isNewFile = @ftp_fput($this->_conn, $file, $fp, FTP_BINARY);
        fclose($fp);
        return $isNewFile && $this->chmod($file, $mode, FileConstants::RECURSIVE_DISABLED);
    }

    /**
     * @ignore
     */
    public function createDir($dir, $mode = 0777)
    {
        if (false === $this->getIsDir($dir)) {
            $this->createDir($this->getDirname($dir), $mode);
            ftp_mkdir($this->_conn, $dir);
        }
        return $this->chmod($dir, $mode, FileConstants::RECURSIVE_DISABLED);
    }

    /**
     * @ignore
     */
    public function deleteFile($file)
    {
        if ($this->getIsFile($file)) {
            $this->chmod($file, 0777, FileConstants::RECURSIVE_DISABLED);
            return ftp_delete($this->_conn, $file);
        }
        return true;
    }

    /**
     * @ignore
     */
    public function deleteDir($dir, $deleteRoot = true)
    {
        if (false === $this->getIsDir($dir)) {
            return true;
        }
        $files = $this->getLists($dir, FileConstants::COMPLETE_PATH);
        foreach ($files as $file) {
            $this->getIsDir($file) ? $this->deleteDir($file) : $this->deleteFile($file);
        }
        return true === $deleteRoot ? ftp_rmdir($this->_conn, $dir) : true;
    }

    /**
     * 复制文件（非递归地）
     *
     * @param string $fromFile
     * @param string $toFile
     *
     * @return boolean
     */
    private function __copyFile($fromFile, $toFile)
    {
        $fp = fopen("php://temp", 'r+');
        ftp_fget($this->_conn, $fp, $fromFile, FTP_BINARY, 0);
        rewind($fp);
        return ftp_fput($this->_conn, $toFile, $fp, FTP_BINARY, 0);
    }

    /**
     * @ignore
     */
    public function copyFile($fromFile, $toFile, $overWrite = false)
    {
        if (false === $this->getIsFile($fromFile)) {
            return false;
        }
        if ($this->getIsFile($toFile)) {
            if (false === $overWrite) {
                return false;
            } else {
                $this->deleteFile($toFile);
            }
        }
        $this->createDir($this->getDirname($toFile));
        return $this->__copyFile($fromFile, $toFile);
    }

    /**
     * @ignore
     */
    public function copyDir($fromDir, $toDir, $overWrite = false)
    {
        $fromDir = rtrim($fromDir, '/') . '/';
        $toDir = rtrim($toDir, '/') . '/';
        if (false === $this->getIsDir($fromDir)) {
            return false;
        }
        $this->createDir($toDir);
        $files = $this->getLists($fromDir, FileConstants::COMPLETE_PATH_DISABLED);
        foreach ($files as $file) {
            if ($this->getIsDir($fromDir . $file)) {
                $this->copyDir($fromDir . $file, $toDir . $file, $overWrite);
            } else {
                $this->copyFile($fromDir . $file, $toDir . $file, $overWrite);
            }
        }
        return true;
    }

    /**
     * @ignore
     */
    public function moveFile($fromFile, $toFile, $overWrite = false)
    {
        if (false === $this->getIsFile($fromFile)) {
            return false;
        }
        if ($this->getIsFile($toFile)) {
            if (false === $overWrite) {
                return false;
            } else {
                $this->deleteFile($toFile);
            }
        }
        $this->createDir($this->getDirname($toFile));
        return ftp_rename($this->_conn, $fromFile, $toFile);
    }

    /**
     * @ignore
     */
    public function moveDir($fromDir, $toDir, $overWrite = false)
    {
        $fromDir = rtrim($fromDir, '/') . '/';
        $toDir = rtrim($toDir, '/') . '/';
        if (false === $this->getIsDir($fromDir)) {
            return false;
        }
        $this->createDir($toDir);
        $files = $this->getLists($fromDir, FileConstants::COMPLETE_PATH_DISABLED);
        foreach ($files as $file) {
            if ($this->getIsDir($fromDir . $file)) {
                $this->moveDir($fromDir . $file, $toDir . $file, $overWrite);
            } else {
                $this->moveFile($fromDir . $file, $toDir . $file, $overWrite);
            }
        }
        return $this->deleteDir($fromDir);
    }
    /**
     * @ignore
     */
    public function chown($file, $user, $flags = FileConstants::RECURSIVE_DISABLED)
    {
        return false;
    }

    /**
     * @ignore
     */
    public function chgrp($file, $group, $recursive = false)
    {
        return false;
    }

    /**
     * @ignore
     */
    public function chmod($file, $mode = 0777, $flags = FileConstants::RECURSIVE_DISABLED)
    {
        if ($this->getIsDir($file) && I::hasFlag($flags, FileConstants::RECURSIVE)) {
            $files = $this->getLists($file, FileConstants::COMPLETE_PATH | FileConstants::RECURSIVE);
            foreach ($files as $subFile) {
                @ftp_chmod($this->_conn, $mode, $subFile);
            }
        }
        return (bool) @ftp_chmod($this->_conn, $mode, $file);
    }

    /**
     * @ignore
     */
    public function symlink($from, $to)
    {
        return false;
    }

    /**
     * 上传文件
     *
     * @param string $toFile 目标文件
     * @param string $fromFile 原文件，如果是 null，表示当前目录下的同名文件
     * @param boolean $overWrite 是否覆盖，默认 true，即：是
     *
     * @return boolean
     */
    public function uploadFile($toFile, $fromFile = null, $overWrite = true)
    {
        null === $fromFile && $fromFile = './' . $this->getBasename($toFile);
        if (false === $overWrite && $this->getIsFile($toFile)) {
            return false;
        }
        $this->createDir($this->getDirname($toFile));
        return ftp_put($this->_conn, $toFile, $fromFile, FTP_BINARY, 0);
    }

    /**
     * 下载文件
     *
     * @param string $fromFile 原文件
     * @param string $toFile 目标文件，如果是 null，表示当前目录下的同名文件
     * @param boolean $overWrite 是否覆盖，默认 true，即：是
     *
     * @return boolean
     */
    public function downloadFile($fromFile, $toFile = null, $overWrite = true)
    {
        null === $toFile && $toFile = './' . $this->getBasename($fromFile);
        if (false === $overWrite && $this->getIsFile($toFile)) {
            return false;
        }
        return ftp_get($this->_conn, $toFile, $fromFile, FTP_BINARY, 0);
    }

    /**
     * @ignore
     */
    public function close()
    {
        return ftp_close($this->_conn);
    }
}
