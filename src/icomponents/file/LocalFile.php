<?php
/**
 * Class LocalFile
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2019, icy2003
 */
namespace icy2003\php\icomponents\file;

use icy2003\php\I;
use icy2003\php\icomponents\file\FileInterface;

/**
 * 本地文件
 *
 * - 支持本地文件操作
 * - 支持网络文件：文件是否存在、文件大小
 */
class LocalFile extends Base implements FileInterface
{

    /**
     * 加载文件用的函数
     *
     * @var callback
     */
    protected $_functions = [
        'loader' => null,
    ];

    /**
     * 文件属性
     *
     * - 文件名为键，属性为值
     *
     * @var array
     */
    protected $_attributes = [];

    /**
     * 初始化
     *
     * @param mixed $locale 地区信息
     */
    public function __construct($locale = 'zh_CN.UTF-8')
    {
        setlocale(LC_ALL, $locale);
        clearstatcache();
        $this->_functions['loader'] = function ($fileName) {
            $hashName = $this->_getHashName($fileName);
            $this->_attributes[$hashName] = I::get($this->_attributes, $hashName, [
                'isCached' => false,
                'isLocal' => true,
                'isExists' => false,
                'fileSize' => 0,
            ]);
            // 如果已经被缓存了，直接返回
            if (true === $this->_attributes[$hashName]['isCached']) {
                return;
            }
            $this->_attributes[$hashName]['isCached'] = true;
            if (preg_match('/^https?:\/\//', $fileName)) {
                $this->_attributes[$hashName]['isLocal'] = false;
                // 加载网络文件
                if (extension_loaded('curl')) {
                    $curl = curl_init($fileName);
                    curl_setopt($curl, CURLOPT_NOBODY, true);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HEADER, true);
                    $result = curl_exec($curl);
                    if ($result && $info = curl_getinfo($curl)) {
                        if (200 == $info['http_code']) {
                            $this->_attributes[$hashName]['isExists'] = true;
                            $this->_attributes[$hashName]['fileSize'] = $info['download_content_length'];
                        }
                    }
                    curl_close($curl);
                } elseif ((bool)ini_get('allow_url_fopen')) {
                    $headArray = (array)get_headers($fileName, true);
                    if (preg_match('/200/', $headArray[0])) {
                        $this->_attributes[$hashName]['isExists'] = true;
                        $this->_attributes[$hashName]['fileSize'] = $headArray['Content-Length'];
                    }
                } else {
                    $url = parse_url($fileName);
                    $host = $url['host'];
                    $path = I::get($url, 'path', '/');
                    $port = I::get($url, 'port', 80);
                    $fp = fsockopen($host, $port);
                    if ((bool)$fp) {
                        $header = [
                            'GET ' . $path . ' HTTP/1.0',
                            'HOST: ' . $host . ':' . $port,
                            'Connection: Close',
                        ];
                        fwrite($fp, implode('\r\n', $header) . '\r\n\r\n');
                        while (!feof($fp)) {
                            $line = fgets($fp);
                            if ('' == trim($line)) {
                                break;
                            } else {
                                preg_match('/HTTP.*(\s\d{3}\s)/', $line, $arr) && $this->_attributes[$hashName]['isExists'] = true;
                                preg_match('/Content-Length:(.*)/si', $line, $arr) && $this->_attributes[$hashName]['fileSize'] = trim($arr[1]);
                            }
                        }
                    }
                    fclose($fp);
                }
            } else {
                $this->_attributes[$hashName]['isLocal'] = true;
                $this->_attributes[$hashName]['spl'] = new \SplFileObject($fileName);
                $this->_attributes[$hashName]['splInfo'] = new \SplFileInfo($fileName);
            }
        };
    }

    /**
     * 获取 Hash 值
     *
     * @param string $fileName
     *
     * @return string
     */
    protected function _getHashName($fileName)
    {
        return md5($fileName);
    }

    /**
     * 获取网络文件的属性
     *
     * @param string $fileName
     * @param string $name
     *
     * @return mixed
     */
    protected function _getFileAttribute($fileName, $name)
    {
        I::trigger($this->_functions['loader'], [$fileName]);
        return $this->_attributes[$this->_getHashName($fileName)][$name];
    }

    /**
     * 获取文件对象
     *
     * @param string $fileName
     *
     * @return \SplFileObject
     */
    public function spl($fileName)
    {
        I::trigger($this->_functions['loader'], [$fileName]);
        return $this->_getFileAttribute($fileName, 'spl');
    }

    /**
     * 获取文件信息对象
     *
     * @param string $fileName
     *
     * @return \SplFileInfo
     */
    public function splInfo($fileName)
    {
        I::trigger($this->_functions['loader'], [$fileName]);
        return $this->_getFileAttribute($fileName, 'splInfo');
    }

    /**
     * 遍历行的生成器
     *
     * - 自动关闭后再次调用需要重新读取文件，不建议自动关闭
     *
     * @param string $fileName
     * @param boolean $autoClose 是否自动关闭文件，默认 false
     *
     * @return \Generator
     */
    public function linesGenerator($fileName, $autoClose = false)
    {
        try {
            $spl = $this->spl($fileName);
            while ($line = $spl->fgets()) {
                yield $line;
            }
        } finally {
            true === $autoClose && $this->close($fileName);
        }
    }

    /**
     * 返回文本的某行
     *
     * - 每取一行，文件指针会回到初始位置，如果需要大量的行，请直接使用 linesGenerator
     * - 自动关闭后再次调用需要重新读取文件，不建议自动关闭
     *
     * @param string $fileName
     * @param integer $num 行号
     * @param boolean $autoClose 是否自动关闭文件，默认 false
     *
     * @return string
     */
    public function line($fileName, $num = 0, $autoClose = false)
    {
        foreach ($this->linesGenerator($fileName, $autoClose) as $k => $line) {
            if ($k == $num) {
                $this->spl($fileName)->rewind();
                return $line;
            }
        }
    }

    /**
     * 遍历字节的生成器
     *
     * @param string $fileName
     * @param boolean $autoClose 是否自动关闭文件，默认 false
     * @param integer $buffer 缓冲区长度，默认 1024
     *
     * @return \Generator
     */
    public function dataGenerator($fileName, $autoClose = false, $buffer = 1024)
    {
        $bufferSize = 0;
        try {
            while (!$this->spl($fileName)->eof() && $this->splInfo($fileName)->getSize() > $bufferSize) {
                $bufferSize += $buffer;
                yield $this->spl($fileName)->fread($bufferSize);
            }
        } finally {
            true === $autoClose && $this->close($fileName);
        }
    }

    /**
     * @ignore
     */
    public function getATime($filename)
    {
        return fileatime($filename);
    }

    /**
     * @ignore
     */
    public function getBasename($path, $suffix = null)
    {
        return parent::getBasename($path, $suffix);
    }

    /**
     * @ignore
     */
    public function getCTime($filename)
    {
        return filectime($filename);
    }

    /**
     * @ignore
     */
    public function getExtension($filename)
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    /**
     * @ignore
     */
    public function getFilename($filename)
    {
        return pathinfo($filename, PATHINFO_FILENAME);
    }

    /**
     * @ignore
     */
    public function getMtime($filename)
    {
        return filemtime($filename);
    }

    /**
     * @ignore
     */
    public function getDirname($path)
    {
        return parent::getDirname($path);
    }

    /**
     * @ignore
     */
    public function getPerms($path)
    {
        return fileperms($path);
    }

    /**
     * @ignore
     */
    public function getFilesize($file)
    {
        I::trigger($this->_functions['loader'], [$file]);
        if (false === $this->_getFileAttribute($file, 'isLocal')) {
            return $this->_getFileAttribute($file, 'fileSize');
        }
        return filesize($file);
    }

    /**
     * @ignore
     */
    public function getType($path)
    {
        return filetype($path);
    }

    /**
     * @ignore
     */
    public function isDir($dir)
    {
        return is_dir($dir);
    }

    /**
     * @ignore
     */
    public function isDot($dir)
    {
        return in_array($this->getBasename($dir), ['.', '..']);
    }

    /**
     * @ignore
     */
    public function isFile($file)
    {
        I::trigger($this->_functions['loader'], [$file]);
        if (false === $this->_getFileAttribute($file, 'isLocal')) {
            return $this->_getFileAttribute($file, 'isExists');
        }
        return file_exists($file);
    }

    /**
     * @ignore
     */
    public function isLink($filename)
    {
        return is_link($filename);
    }

    /**
     * @ignore
     */
    public function isReadable($path)
    {
        return is_readable($path);
    }

    /**
     * @ignore
     */
    public function isWritable($path)
    {
        return is_writable($path);
    }

    /**
     * @ignore
     */
    public function getCommandResult($command)
    {
        return false;
    }

    /**
     * @ignore
     */
    public function getRealpath($path)
    {
        return realpath($path);
    }

    /**
     * @ignore
     */
    public function getLists($dir = null, $flags = FileConstants::COMPLETE_PATH)
    {
        null === $dir && $dir = $this->getRealpath('./');
        $dir = rtrim($dir, '/') . '/';
        $iterator = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS);
        if (I::hasFlag($flags, FileConstants::RECURSIVE)) {
            $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);
        }
        $files = [];
        /**
         * @var \RecursiveDirectoryIterator $file
         */
        foreach ($iterator as $file) {
            if (I::hasFlag($flags, FileConstants::COMPLETE_PATH)) {
                $files[] = $file->getPathname();
            } else {
                $files[] = $file->getFilename();
            }
        }
        return $files;
    }

    /**
     * @ignore
     */
    public function getFileContent($file)
    {
        return file_get_contents($file);
    }

    /**
     * @ignore
     */
    public function putFileContent($file, $string, $mode = 0777)
    {
        $this->createDir($this->getDirname($file), $mode);
        $isCreated = false !== file_put_contents($file, $string);
        $this->chmod($file, $mode, FileConstants::RECURSIVE_DISABLED);
        return $isCreated;
    }

    /**
     * @ignore
     */
    public function deleteFile($file)
    {
        if ($this->isFile($file)) {
            return unlink($file);
        }
        return true;
    }

    /**
     * @ignore
     */
    public function uploadFile($toFile, $fromFile = null, $overwrite = true)
    {
        return false;
    }

    /**
     * @ignore
     */
    public function downloadFile($fromFile, $toFile = null, $overwrite = true)
    {
        return false;
    }

    /**
     * @ignore
     */
    public function chown($file, $user, $flags = FileConstants::RECURSIVE_DISABLED)
    {
        if ($this->isDir($file) && I::hasFlag($flags, FileConstants::RECURSIVE)) {
            $files = $this->getLists($file, FileConstants::COMPLETE_PATH | FileConstants::RECURSIVE);
            foreach ($files as $subFile) {
                chown($subFile, $user);
            }
        }
        return chown($file, $user);
    }

    /**
     * @ignore
     */
    public function chgrp($file, $group, $flags = FileConstants::RECURSIVE_DISABLED)
    {
        if ($this->isDir($file) && I::hasFlag($flags, FileConstants::RECURSIVE)) {
            $files = $this->getLists($file, FileConstants::COMPLETE_PATH | FileConstants::RECURSIVE);
            foreach ($files as $subFile) {
                chgrp($subFile, $group);
            }
        }
        return chgrp($file, $group);
    }

    /**
     * @ignore
     */
    public function chmod($file, $mode = 0777, $flags = FileConstants::RECURSIVE_DISABLED)
    {
        if ($this->isDir($file) && I::hasFlag($flags, FileConstants::RECURSIVE)) {
            $files = $this->getLists($file, FileConstants::COMPLETE_PATH | FileConstants::RECURSIVE);
            foreach ($files as $subFile) {
                chmod($subFile, $mode);
            }
        }
        return (bool) chmod($file, $mode);
    }

    /**
     * @ignore
     */
    public function symlink($from, $to)
    {
        return symlink($from, $to);
    }

    /**
     * @ignore
     */
    public function close($fileName = null)
    {
        if (is_string($fileName)) {
            $fileName = [$this->_getHashName($fileName)];
        } elseif (is_array($fileName)) {
            foreach ($fileName as $k => $name) {
                $fileName[$k] = $this->_getHashName($name);
            }
        }
        foreach ($this->_attributes as $hashName => /** @scrutinizer ignore-unused */$attribute) {
            if (null === $fileName || is_array($fileName) && in_array($hashName, $fileName)) {
                unset($this->_attributes[$hashName]);
            }
        }
        return true;
    }

    /**
     * @ignore
     */
    protected function _copy($fromFile, $toFile)
    {
        return copy($fromFile, $toFile);
    }

    /**
     * @ignore
     */
    protected function _move($fromFile, $toFile)
    {
        return rename($fromFile, $toFile);
    }

    /**
     * @ignore
     */
    protected function _mkdir($dir, $mode = 0777)
    {
        return mkdir($dir, $mode);
    }

    /**
     * @ignore
     */
    protected function _rmdir($dir)
    {
        return rmdir($dir);
    }

}
