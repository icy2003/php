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
        if (false === ftp_login($this->_conn, I::value($config, 'username'), I::value($config, 'password'))) {
            throw new \Exception("账号密码错误");
        }
        ftp_pasv($this->_conn, true);
    }

    /**
     * 是否是一个文件
     *
     * @param string $file 文件的路径
     *
     * @return boolean
     */
    public function getIsFile($file)
    {
        return ftp_size($this->_conn, $file) > -1;
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
        return ($current = @ftp_pwd($this->_conn)) &&
        !!@ftp_chdir($this->_conn, $dir) &&
        ftp_chdir($this->_conn, $current);
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
        null === $dir && $dir = ftp_pwd($this->_conn);
        return ftp_nlist($this->_conn, $dir);
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
        return ftp_size($this->_conn, $file);
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
     * 创建文件（目录会被递归地创建）
     *
     * @param string $file 文件的路径
     * @param integer $mode 默认的 mode 是 0777，意味着最大可能的访问权
     *
     * @return boolean
     */
    public function createFile($file, $mode = 0777)
    {
        return $this->getIsFile($file) ||
        $this->createDir(File::dirname($file), $mode) &&
        $this->__createFile($file) &&
        @ftp_chmod($this->_conn, $mode, $file);
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
        return $this->getIsDir($dir) ||
        $this->createDir(File::dirname($dir), $mode) &&
        ftp_mkdir($this->_conn, $dir) &&
        ftp_chmod($this->_conn, $mode, $dir);
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
        if ($this->getIsFile($file)) {
            @ftp_chmod($this->_conn, 0777, $file);
            return ftp_delete($this->_conn, $file);
        }
        return true;
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
        if (false === $this->getIsDir($dir)) {
            return true;
        }
        $files = $this->getLists($dir);
        foreach ($files as $file) {
            $file = File::basename($file);
            $this->getIsDir($dir . '/' . $file) ? $this->deleteDir($dir . '/' . $file) : $this->deleteFile($dir . '/' . $file);
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
        $this->createDir(File::dirname($toFile));
        $this->__copyFile($fromFile, $toFile);
        return true;
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
        $fromDir = rtrim($fromDir, '/') . '/';
        $toDir = rtrim($toDir, '/') . '/';
        if (false === $this->getIsDir($fromDir)) {
            return false;
        }
        $files = $this->getLists($fromDir);
        $this->createDir($toDir);
        foreach ($files as $file) {
            $file = $this->getBasename($file);
            if ($this->getIsDir($fromDir . $file)) {
                $this->copyDir($fromDir . $file, $toDir . $file, $overWrite);
            } else {
                $this->copyFile($fromDir . $file, $toDir . $file, $overWrite);
            }
        }
        return true;
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
        ftp_rename($this->_conn, $fromFile, $toFile);
        return true;
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
        $fromDir = rtrim($fromDir, '/') . '/';
        $toDir = rtrim($toDir, '/') . '/';
        if (false === $this->getIsDir($fromDir)) {
            return false;
        }
        $files = $this->getLists($fromDir);
        foreach ($files as $file) {
            $file = $this->getBasename($file);
            if ($this->getIsDir($fromDir . $file)) {
                $this->moveDir($fromDir . $file, $toDir . $file, $overWrite);
            } else {
                $this->moveFile($fromDir . $file, $toDir . $file, $overWrite);
            }
        }
        return $this->deleteDir($fromDir);
    }

    /**
     * 上传文件
     *
     * @param string $toFile 目标文件
     * @param string $fromFile 原文件，如果是 null，表示当前目录下的同名文件
     *
     * @return boolean
     */
    public function uploadFile($toFile, $fromFile = null)
    {
        null === $fromFile && $fromFile = './' . $this->getBasename($toFile);
        $this->createDir($this->getDirname($toFile));
        return ftp_put($this->_conn, $toFile, $fromFile, FTP_BINARY, 0);
    }

    /**
     * 下载文件
     *
     * @param string $fromFile 原文件
     * @param string $toFile 目标文件，如果是 null，表示当前目录下的同名文件
     *
     * @return boolean
     */
    public function downloadFile($fromFile, $toFile = null)
    {
        null === $toFile && $toFile = './' . $this->getBasename($fromFile);
        return ftp_get($this->_conn, $toFile, $fromFile, FTP_BINARY, 0);
    }

    /**
     * 关闭 ftp 连接
     */
    public function close()
    {
        ftp_close($this->_conn);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->close();
    }
}
