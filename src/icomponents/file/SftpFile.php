<?php
/**
 * Class SftpFile
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
 * sftp
 *
 * - 由于 PHP 里 sftp 提供的函数相对较少，为了让代码不那么诡异，这里用了很多 shell 命令
 * - Windows 平台自求多福
 */
class SftpFile extends Base
{
    /**
     * ssh 连接
     *
     * @var resource
     */
    protected $_conn;

    /**
     * sftp 对象
     *
     * @var resource
     */
    protected $_sftp;

    /**
     * 构造函数
     *
     * @param array $config sftp 连接配置。至少包括：host、username、password，可选：port、methods、callback
     */
    public function __construct($config)
    {
        C::assertFunction('ssh2_connect', '请开启 ssh2 扩展');
        C::assertTrue(Arrays::keyExistsAll(['host', 'username', 'password'], $config, $diff), '缺少 ' . implode(',', $diff) . ' 参数');
        $this->_conn = ssh2_connect(
            (string) I::get($config, 'host'),
            (int) I::get($config, 'port', 22),
            (array) I::get($config, 'methods', []),
            (array) I::get($config, 'callback', [])
        );
        C::assertNotFalse($this->_conn, '连接失败');
        C::assertNotFalse(ssh2_auth_password($this->_conn, (string) I::get($config, 'username'), (string) I::get($config, 'password')), '账号密码错误');
        $this->_sftp = ssh2_sftp($this->_conn);
        C::assertNotFalse($this->_sftp, '初始化 sftp 失败');
    }

    /**
     * @ignore
     */
    public function getCommandResult($command)
    {
        $stream = ssh2_exec($this->_conn, $command);
        stream_set_blocking($stream, true);
        $result = stream_get_contents($stream);
        fclose($stream);
        return $result;
    }

    /**
     * 获取 ssh2 协议格式的文件路径
     *
     * @param string $file 文件（目录）的路径
     *
     * @return string
     */
    private function __getFilepath($file)
    {
        return rtrim('ssh2.sftp://' . intval($this->_sftp), '/') . $file;
    }

    /**
     * @ignore
     */
    public function isFile($file)
    {
        return file_exists($this->__getFilepath($file));
    }

    /**
     * @ignore
     */
    public function isDir($dir)
    {
        return is_dir($this->__getFilepath($dir));
    }

    /**
     * @ignore
     */
    public function getRealpath($path)
    {
        return ssh2_sftp_realpath($this->_sftp, $path);
    }

    /**
     * @ignore
     */
    public function getLists($dir = null, $flags = FileConstants::COMPLETE_PATH)
    {
        $list = [];
        null === $dir && $dir = rtrim($this->getCommandResult('pwd'), PHP_EOL);
        $dir = rtrim($dir, '/') . '/';
        $command = 'ls -m "' . $dir . '"';
        $listString = rtrim($this->getCommandResult($command), PHP_EOL);
        $files = explode(', ', $listString);
        foreach ($files as $file) {
            $file = $this->getBasename($file);
            if (I::hasFlag($flags, FileConstants::RECURSIVE) && $this->isDir($dir . $file)) {
                $list = array_merge($list, $this->getLists($dir . $file, $flags));
            }
            $list[] = I::hasFlag($flags, FileConstants::COMPLETE_PATH) ? $dir . $file : $file;
        }
        return $list;
    }

    /**
     * @ignore
     */
    public function getFilesize($file)
    {
        return filesize($this->__getFilepath($file));
    }

    /**
     * @ignore
     */
    public function getFileContent($file)
    {
        return file_get_contents($this->__getFilepath($file));
    }

    /**
     * @ignore
     */
    public function putFileContent($file, $string, $mode = 0777)
    {
        $this->createDir($this->getDirname($file));
        $isCreated = false !== file_put_contents($this->__getFilepath($file), $string);
        $this->chmod($file, $mode, FileConstants::RECURSIVE_DISABLED);
        return $isCreated;
    }

    /**
     * @ignore
     */
    public function createDir($dir, $mode = 0777)
    {
        return ssh2_sftp_mkdir($this->_sftp, $dir, $mode, true);
    }

    /**
     * @ignore
     */
    public function deleteFile($file)
    {
        if ($this->isFile($file)) {
            return ssh2_sftp_unlink($this->_sftp, $file);
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
        return copy($fromFile, $this->__getFilepath($toFile));
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
        return copy($this->__getFilepath($fromFile), $toFile);
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
                /** @scrutinizer ignore-unhandled */ @ssh2_sftp_chmod($this->_sftp, $subFile, $mode);
            }
        }
        return /** @scrutinizer ignore-unhandled */ @ssh2_sftp_chmod($this->_sftp, $subFile, $mode);
    }

    /**
     * @ignore
     */
    public function symlink($from, $to)
    {
        return ssh2_sftp_symlink($this->_sftp, $from, $to);
    }

    /**
     * @ignore
     */
    public function close()
    {
        return ssh2_disconnect($this->_conn);
    }

    /**
     * @ignore
     */
    protected function _copy($fromFile, $toFile)
    {
        return copy($this->__getFilepath($fromFile), $this->__getFilepath($toFile));
    }

    /**
     * @ignore
     */
    protected function _move($fromFile, $toFile)
    {
        return ssh2_sftp_rename($this->_sftp, $fromFile, $toFile);
    }

    /**
     * @ignore
     */
    protected function _mkdir($dir, $mode = 0777)
    {
        return ssh2_sftp_mkdir($this->_sftp, $dir, $mode);
    }

    /**
     * @ignore
     */
    protected function _rmdir($dir)
    {
        return ssh2_sftp_rmdir($this->_sftp, $dir);
    }
}
