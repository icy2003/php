<?php
/**
 * abstract Class Base
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\icomponents\file;
use icy2003\php\ihelpers\Charset;
use icy2003\php\ihelpers\Arrays;

/**
 * 文件抽象类
 */
abstract class Base
{
    /**
     * 获得命令返回值
     *
     * - 不要依赖这个，一些环境不一定支持
     *
     * @param string $command 命令
     *
     * @return string
     */
    abstract public function getCommandResult($command);

    /**
     * @ignore
     */
    public function getBasename($path, $suffix = null)
    {
        $path = str_replace('\\', '/', $path);
        return basename($path, $suffix);
    }

    /**
     * @ignore
     */
    public function getDirname($path)
    {
        $path = str_replace('\\', '/', $path);
        return dirname($path);
    }

    /**
     * @ignore
     */
    abstract public function isFile($file);

    /**
     * @ignore
     */
    abstract public function isDir($dir);

    /**
     * 返回规范化的绝对路径名
     *
     * - 不支持 realpath 的类将使用这个，其他的由子类实现
     * - 该函数实现是：处理输入的 path 中的 '/./', '/../' 以及多余的 '/'
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
     * @return string|false
     */
    abstract public function getFileContent($file);

    /**
     * 创建一个文件（目录会被递归地创建），并用字符串（资源）填充进文件
     *
     * - 可将字符串、资源、数组写入文件，行为和 file_put_contents 一样
     * - 资源：`$fp = fopen('https://www.icy2003.com', 'r');`
     *
     * @link https://www.php.net/manual/zh/function.file-put-contents.php
     *
     * @param string $file 文件的路径
     * @param string|array $string 待填充进文件的字符串（资源）
     * @param integer $mode 默认的 mode 是 0777，意味着最大可能的访问权
     *
     * @return boolean
     */
    abstract public function putFileContent($file, $string, $mode = 0777);

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
        if ($this->isDir($dir)) {
            return true;
        }
        $this->createDir($this->getDirname($dir), $mode);
        return $this->_mkdir($dir, $mode);
    }

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
    public function deleteDir($dir, $deleteRoot = true)
    {
        if (false === $this->isDir($dir)) {
            return true;
        }
        $files = $this->getLists($dir, FileConstants::COMPLETE_PATH);
        foreach ($files as $file) {
            $this->isDir($file) ? $this->deleteDir($file) : $this->deleteFile($file);
        }

        return true === $deleteRoot ? $this->_rmdir($dir) : true;
    }

    /**
     * 复制文件（目录会被递归地创建）
     *
     * @param string $fromFile 文件原路径
     * @param string $toFile 文件目标路径
     * @param boolean $overwrite 已存在的文件是否被覆盖，默认 false，即不覆盖
     *
     * @return boolean
     */
    public function copyFile($fromFile, $toFile, $overwrite = false)
    {
        if (false === $this->isFile($fromFile)) {
            return false;
        }
        if ($this->isFile($toFile)) {
            if (false === $overwrite) {
                return false;
            } else {
                $this->deleteFile($toFile);
            }
        }
        $this->createDir($this->getDirname($toFile));
        return $this->_copy($fromFile, $toFile);
    }

    /**
     * 递归地复制目录
     *
     * @param string $fromDir 目录原路径
     * @param string $toDir 目录目标路径
     * @param boolean $overwrite 已存在的目录是否被覆盖，默认 false，即不覆盖
     *
     * @return boolean
     */
    public function copyDir($fromDir, $toDir, $overwrite = false)
    {
        $fromDir = rtrim($fromDir, '/') . '/';
        $toDir = rtrim($toDir, '/') . '/';
        if (false === $this->isDir($fromDir)) {
            return false;
        }
        $this->createDir($toDir);
        $files = $this->getLists($fromDir, FileConstants::COMPLETE_PATH_DISABLED);
        foreach ($files as $file) {
            if ($this->isDir($fromDir . $file)) {
                $this->copyDir($fromDir . $file, $toDir . $file, $overwrite);
            } else {
                $this->copyFile($fromDir . $file, $toDir . $file, $overwrite);
            }
        }
        return true;
    }

    /**
     * 移动文件（目录会被递归地创建）
     *
     * @param string $fromFile 文件原路径
     * @param string $toFile 文件目标路径
     * @param boolean $overwrite 已存在的文件是否被覆盖，默认 false，即不覆盖
     *
     * @return boolean
     */
    public function moveFile($fromFile, $toFile, $overwrite = false)
    {
        if (false === $this->isFile($fromFile)) {
            return false;
        }
        if ($this->isFile($toFile)) {
            if (false === $overwrite) {
                return false;
            } else {
                $this->deleteFile($toFile);
            }
        }
        $this->createDir($this->getDirname($toFile));
        return $this->_move($fromFile, $toFile);
    }

    /**
     * 递归地移动目录
     *
     * @param string $fromDir 目录原路径
     * @param string $toDir 目录目标路径
     * @param boolean $overwrite 已存在的目录是否被覆盖，默认 false，即不覆盖
     *
     * @return boolean
     */
    public function moveDir($fromDir, $toDir, $overwrite = false)
    {
        $fromDir = rtrim($fromDir, '/') . '/';
        $toDir = rtrim($toDir, '/') . '/';
        if (false === $this->isDir($fromDir)) {
            return false;
        }
        $this->createDir($toDir);
        $files = $this->getLists($fromDir, FileConstants::COMPLETE_PATH_DISABLED);
        foreach ($files as $file) {
            if ($this->isDir($fromDir . $file)) {
                $this->moveDir($fromDir . $file, $toDir . $file, $overwrite);
            } else {
                $this->moveFile($fromDir . $file, $toDir . $file, $overwrite);
            }
        }
        return $this->deleteDir($fromDir);
    }

    /**
     * 上传文件
     *
     * @param string|array $fileMap @see self::fileMap()
     * @param boolean $overwrite 是否覆盖，默认 true，即：是
     *
     * @return boolean
     */
    abstract public function uploadFile($fileMap, $overwrite = true);

    /**
     * 下载文件
     *
     * @param string|array $fileMap @see self::fileMap()
     * @param boolean $overwrite 是否覆盖，默认 true，即：是
     *
     * @return boolean
     */
    abstract public function downloadFile($fileMap, $overwrite = true);

    /**
     * 改变文件（目录）的创建者
     *
     * - 不支持的类：FtpFile
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
     * - 不支持的类：FtpFile
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
     * - 不支持的类：FtpFile
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
     * 非递归地复制目录（文件）
     *
     * @param string $fromFile 源目录（文件）的路径
     * @param string $toFile 目标目录（文件）的路径
     *
     * @return boolean
     */
    abstract protected function _copy($fromFile, $toFile);

    /**
     * 非递归地移动目录（文件）
     *
     * @param string $fromFile 源目录（文件）的路径
     * @param string $toFile 目标目录（文件）的路径
     *
     * @return boolean
     */
    abstract protected function _move($fromFile, $toFile);

    /**
     * 非递归地创建目录
     *
     * @param string $dir 目录的路径
     * @param integer $mode 默认的 mode 是 0777，意味着最大可能的访问权
     *
     * @return boolean
     */
    abstract protected function _mkdir($dir, $mode = 0777);

    /**
     * 非递归地删除目录
     *
     * @param string $dir
     *
     * @return boolean
     */
    abstract protected function _rmdir($dir);

    /**
     * 析构函数：关闭文件句柄（连接）
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * 返回文件映射
     *
     * - 支持别名
     *
     * @param string|array $file 数组：[[带路径的文件名],[不带路径的文件名]]，字符串会转成数组
     *
     * @return array
     */
    public function fileMap($file)
    {
        if (is_string($file)) {
            $file = [$file, Charset::toCn($this->getBasename($file))];
        } elseif (is_array($file)) {
            $file = Arrays::lists($file, 2);
            if ($this->isDir($file[1])) {
                $file[1] = rtrim($file[1], '/') . '/' . Charset::toCn($this->getBasename($file[0]));
            }
        } else {
            $file = ['', ''];
        }
        return $file;
    }
}
