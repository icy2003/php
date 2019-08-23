<?php
/**
 * Class FtpFile
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2019, icy2003
 */
namespace icy2003\php\icomponents\file;

use icy2003\php\C;
use icy2003\php\I;
use icy2003\php\ihelpers\Arrays;

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
        C::assertTrue(function_exists('ftp_connect'), '请开启 ftp 扩展');
        C::assertTrue(Arrays::keyExistsAll(['host', 'username', 'password'], $config, $diff), '缺少 ' . implode(',', $diff) . ' 参数');
        $this->_conn = ftp_connect((string) I::get($config, 'host'), (int) I::get($config, 'port', 21), (int) I::get($config, 'timeout', 90));
        C::assertTrue(is_resource($this->_conn), '连接失败');
        C::assertTrue(@ftp_login($this->_conn, (string) I::get($config, 'username'), (string) I::get($config, 'password')), '账号密码错误');
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
    public function isFile($file)
    {
        return ftp_size($this->_conn, $file) > -1;
    }

    /**
     * @ignore
     */
    public function isDir($dir)
    {
        $current = ftp_pwd($this->_conn);
        if (@ftp_chdir($this->_conn, $dir)) {
            ftp_chdir($this->_conn, $current);
            return true;
        }
        return false;
    }

    /**
     * @ignore
     */
    public function getLists($dir = null, $flags = FileConstants::COMPLETE_PATH)
    {
        $list = [];
        null === $dir && $dir = ftp_pwd($this->_conn);
        $dir = rtrim($dir, '/') . '/';
        $files = ftp_nlist($this->_conn, $dir);
        foreach ($files as $file) {
            if (I::hasFlag($flags, FileConstants::RECURSIVE) && $this->isDir($file)) {
                $list = array_merge($list, $this->getLists($file, $flags));
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
        if ($fp = fopen('php://temp', 'r+')) {
            if (@ftp_fget($this->_conn, $fp, $file, FTP_BINARY, 0)) {
                rewind($fp);
                $content = stream_get_contents($fp);
                fclose($fp);
                return $content;
            }
        }
        return false;
    }

    /**
     * @ignore
     */
    public function putFileContent($file, $string, $mode = 0777)
    {
        if (is_array($string)) {
            $string = implode('', $string);
        } elseif (is_resource($string)) {
            $string = stream_get_contents($string);
        }
        $this->createDir($this->getDirname($file));
        if ($fp = fopen('data://text/plain,' . $string, 'r')) {
            $isNewFile = @ftp_fput($this->_conn, $file, $fp, FTP_BINARY);
            fclose($fp);
            $this->chmod($file, $mode, FileConstants::RECURSIVE_DISABLED);
            return $isNewFile;
        }
        return false;
    }

    /**
     * @ignore
     */
    public function deleteFile($file)
    {
        if ($this->isFile($file)) {
            return ftp_delete($this->_conn, $file);
        }
        return true;
    }

    /**
     * @ignore
     */
    public function uploadFile($toFile, $fromFile = null, $overwrite = true)
    {
        null === $fromFile && $fromFile = './' . $this->getBasename($toFile);
        if (false === $overwrite && $this->isFile($toFile)) {
            return false;
        }
        $this->createDir($this->getDirname($toFile));
        return ftp_put($this->_conn, $toFile, $fromFile, FTP_BINARY, 0);
    }

    /**
     * @ignore
     */
    public function downloadFile($fromFile, $toFile = null, $overwrite = true)
    {
        null === $toFile && $toFile = './' . $this->getBasename($fromFile);
        if (false === $overwrite && $this->isFile($toFile)) {
            return false;
        }
        return ftp_get($this->_conn, $toFile, $fromFile, FTP_BINARY, 0);
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
    public function chgrp($file, $group, $flags = FileConstants::RECURSIVE_DISABLED)
    {
        return false;
    }

    /**
     * @ignore
     */
    public function chmod($file, $mode = 0777, $flags = FileConstants::RECURSIVE_DISABLED)
    {
        if ($this->isDir($file) && I::hasFlag($flags, FileConstants::RECURSIVE)) {
            $files = $this->getLists($file, FileConstants::COMPLETE_PATH | FileConstants::RECURSIVE);
            foreach ($files as $subFile) {
                /** @scrutinizer ignore-unhandled */@ftp_chmod($this->_conn, $mode, $subFile);
            }
        }
        return (bool) /** @scrutinizer ignore-unhandled */@ftp_chmod($this->_conn, $mode, $file);
    }

    /**
     * @ignore
     */
    public function symlink($from, $to)
    {
        return false;
    }

    /**
     * @ignore
     */
    public function close()
    {
        return ftp_close($this->_conn);
    }

    /**
     * @ignore
     */
    protected function _copy($fromFile, $toFile)
    {
        if ($fp = fopen("php://temp", 'r+')) {
            @ftp_fget($this->_conn, $fp, $fromFile, FTP_BINARY, 0);
            rewind($fp);
            return @ftp_fput($this->_conn, $toFile, $fp, FTP_BINARY, 0);
        }
        return false;
    }

    /**
     * @ignore
     */
    protected function _move($fromFile, $toFile)
    {
        return @ftp_rename($this->_conn, $fromFile, $toFile);
    }

    /**
     * @ignore
     */
    protected function _mkdir($dir, $mode = 0777)
    {
        $isCreated = (bool) @ftp_mkdir($this->_conn, $dir);
        $this->chmod($dir, $mode, FileConstants::RECURSIVE_DISABLED);
        return $isCreated;
    }

    /**
     * @ignore
     */
    protected function _rmdir($dir)
    {
        return @ftp_rmdir($this->_conn, $dir);
    }
}
