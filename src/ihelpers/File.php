<?php
/**
 * Class File
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

use icy2003\php\I;

/**
 * 文件类
 *
 * @todo create 单例会有两次请求，一次获取文件属性，一次用于读取文件
 */
class File
{

    /**
     * 文件是否存在
     *
     * @param string $file 文件路径
     *
     * @return boolean
     */
    public static function fileExists($file)
    {
        return file_exists($file);
    }

    /**
     * 原生函数 basename 会在非 windows 系统区分 `/` 和 `\`，该函数并不会
     *
     * @see https://www.php.net/manual/zh/function.basename.php
     *
     * @param string $path 文件路径
     * @param string $suffix 文件后缀
     *
     * @return string
     */
    public static function basename($path, $suffix = null)
    {
        $path = str_replace('\\', '/', $path);
        return basename($path, $suffix);
    }

    /**
     * 原生函数 dirname 会在非 windows 系统区分 `/` 和 `\`，该函数并不会
     *
     * @see https://www.php.net/manual/zh/function.dirname.php
     *
     * @param string $path 目录路径
     *
     * @return string
     */
    public static function dirname($path)
    {
        $path = str_replace('\\', '/', $path);
        return dirname($path);
    }

    /**
     * 创建一个目录（递归）.
     *
     * @param string $dir 目录路径
     * @param int 目录权限，默认 777
     *
     * @return bool 是否成功创建
     */
    public static function createDir($dir, $mode = 0777)
    {
        return is_dir($dir) || self::createDir(dirname($dir)) && mkdir($dir, $mode);
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
        return self::fileExists($file) || self::createDir(dirname($file)) && touch($file);
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
        if (self::fileExists($file)) {
            @chmod($file, 0777);
            return unlink($file);
        }
        return true;
    }

    /**
     * 删除一个目录（递归）.
     *
     * @param string $dir 目录路径
     * @param bool $deleteRoot 是否删除根目录，默认是
     *
     * @return bool 是否成功删除
     */
    public static function deleteDir($dir, $deleteRoot = true)
    {
        // glob 函数拿不到隐藏文件
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            is_dir($dir . '/' . $file) ? self::deleteDir($dir . '/' . $file) : self::deleteFile($dir . '/' . $file);
        }

        return true === $deleteRoot ? rmdir($dir) : true;
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
        if (!self::fileExists($fromFile)) {
            return false;
        }
        if (self::fileExists($toFile)) {
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
        $fromDir = rtrim($fromDir, '/') . '/';
        $toDir = rtrim($toDir, '/') . '/';
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
            if (is_dir($fromDir . $file)) {
                self::copyDir($fromDir . $file, $toDir . $file, $overWrite);
            } else {
                self::copyFile($fromDir . $file, $toDir . $file, $overWrite);
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
        if (!self::fileExists($fromFile)) {
            return false;
        }
        if (self::fileExists($toFile)) {
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
        $fromDir = rtrim($fromDir, '/') . '/';
        $toDir = rtrim($toDir, '/') . '/';
        if (!is_dir($fromDir)) {
            return false;
        }
        if (false === ($dirHanlder = opendir($fromDir))) {
            return false;
        }
        self::createDir($toDir);
        while (false !== ($file = readdir($dirHanlder))) {
            if ('.' === $file || '..' === $file) {
                continue;
            }
            if (is_dir($fromDir . $file)) {
                self::moveDir($fromDir . $file, $toDir . $file, $overWrite);
            } else {
                self::moveFile($fromDir . $file, $toDir . $file, $overWrite);
            }
        }
        closedir($dirHanlder);

        return self::deleteDir($fromDir);
    }

    // 读文件

    /**
     * 单例对象
     *
     * @var static
     */
    protected static $_instance;

    /**
     * 构造函数
     *
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * 克隆函数
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * 创建单例
     *
     * @return static
     */
    public static function create()
    {
        if (!static::$_instance instanceof static ) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    /**
     * 文件信息标准接口
     * @var \SplFileInfo
     */
    private $__fileInfoHandler;

    /**
     * 文件对象标准接口
     * @var \SplFileObject
     */
    private $__fileObjectHandler;

    /**
     * 缓存文件对象标准接口
     *
     * @var \SplTempFileObject
     */
    private $__tempFileObjectHandler;

    /**
     * 属性列表
     *
     * @var array
     */
    private $__attributes = [
        'isExists' => false,
        'fileSize' => 0,
        'fileType' => '',
        'fileName' => '',
        'filePath' => '',
        'isLocal' => true,
    ];

    /**
     * 加载一个文件
     *
     * @param string $fileName
     * @return static
     */
    public function loadFile($fileName)
    {
        $fileName = I::getAlias($fileName);
        if (preg_match('/^https?:\/\//', $fileName)) {
            $this->__attributes['isLocal'] = false;
            // 加载网络文件
            if (extension_loaded('curl')) {
                $curl = curl_init($fileName);
                curl_setopt($curl, CURLOPT_NOBODY, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HEADER, true);
                $result = curl_exec($curl);
                if ($result && $info = curl_getinfo($curl)) {
                    if (200 == $info['http_code']) {
                        $this->__attributes['isExists'] = true;
                        $this->__attributes['fileSize'] = $info['download_content_length'];
                        $this->__attributes['fileType'] = $info['content_type'];
                    }
                }
                curl_close($curl);
            } elseif (ini_get('allow_url_fopen')) {
                $headArray = get_headers($fileName, true);
                if (preg_match('/200/', $headArray[0])) {
                    $this->__attributes['isExists'] = true;
                    $this->__attributes['fileSize'] = $headArray['Content-Length'];
                    $this->__attributes['fileType'] = $headArray['Content-Type'];
                }
            } else {
                $url = parse_url($fileName);
                $host = $url['host'];
                $path = I::value($url, 'path', '/');
                $port = I::value($url, 'port', 80);
                $fp = fsockopen($host, $port, $errno, $error);
                if ($fp) {
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
                            preg_match('/HTTP.*(\s\d{3}\s)/', $line, $arr) && $this->__attributes['isExists'] = true;
                            preg_match('/Content-Length:(.*)/si', $line, $arr) && $this->__attributes['fileSize'] = trim($arr[1]);
                            preg_match('/Content-Type:(.*)/si', $line, $arr) && $this->__attributes['fileType'] = trim($arr[1]);
                        }
                    }
                }
                fclose($fp);
            }
        } else {
            if ($this->__attributes['isExists'] = self::fileExists($fileName)) {
                $this->__attributes['fileSize'] = filesize($fileName);
                $this->__attributes['fileType'] = filetype($fileName);
            }
        }
        $this->__attributes['fileName'] = static::basename($fileName);
        $this->__attributes['filePath'] = $fileName;
        if (false === $this->__attributes['isExists']) {
            throw new \Exception('文件 ' . $fileName . ' 不存在');
        }
        $this->__fileInfoHandler = \SplFileInfo($fileName);
        $this->__fileObjectHandler = \SplFileObject($fileName);
        $this->__tempFileObjectHandler = \SplTempFileObject($fileName);
        return $this;
    }

    /**
     * 获取文件属性
     *
     * @param string $name         属性名
     * @param mixed  $defaultValue 默认值
     *
     * @return mixed
     */
    public function getAttribute($name, $defaultValue = null)
    {
        return I::value($this->__attributes, $name, $defaultValue);
    }

    /**
     * 获取文件所有属性
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->__attributes;
    }

    /**
     * 是否是本地文件
     *
     * @return boolean
     */
    public function isLocal()
    {
        return (bool)$this->getAttribute('isLocal');
    }

    /**
     * SplFileInfo
     *
     * @return \SplFileInfo
     */
    public function splFileInfo()
    {
        return $this->__fileInfoHandler;
    }

    /**
     * SplFileObject
     *
     * @return \SplFileObject
     */
    public function splFileObject()
    {
        return $this->__fileObjectHandler;
    }

    /**
     * SplTempFileObject
     *
     * @return \SplTempFileObject
     */
    public function splTempFileObject()
    {
        return $this->__tempFileObjectHandler;
    }

    /**
     * 关闭文件
     *
     * @return void
     */
    public function fClose()
    {
        $this->__fileInfoHandler = null;
        $this->__fileObjectHandler = null;
        $this->__tempFileObjectHandler = null;
    }

    /**
     * 遍历行的生成器
     *
     * @param boolean $autoClose 是否自动关闭文件，默认是
     *
     * @return \Generator
     */
    public function lines($autoClose = true)
    {
        try {
            while ($line = $this->__fileObjectHandler->fgets()) {
                yield $line;
            }
        } finally {
            $autoClose && $this->fClose();
        }
    }

    /**
     * 返回文本的某行，从 0 行开始
     *
     * @param int $num 默认值
     * @param boolean $autoClose 是否自动关闭文件，默认否（考虑到可能还要取某行，所以不会自动关闭文件）
     *
     * @return string
     */
    public function line($num = 0, $autoClose = false)
    {
        foreach ($this->lines($autoClose) as $k => $line) {
            if ($k == $num) {
                return $line;
            }
        }
    }

    /**
     * 遍历字节的生成器
     *
     * @param boolean $autoClose 是否自动关闭文件，默认是
     * @param int $buffer 缓冲区长度，默认 1024
     *
     * @return \Generator
     */
    public function data($autoClose = true, $buffer = 1024)
    {
        $bufferSize = 0;
        try {
            while (!$this->__fileObjectHandler->eof() && $this->__fileInfoHandler->getSize() > $bufferSize) {
                $bufferSize += $buffer;
                yield $this->__fileObjectHandler->fread($buffer);
            }
        } finally {
            $autoClose && $this->fClose();
        }
    }

    /**
     * 目录迭代器
     *
     * @var \DirectoryIterator
     */
    private $__dirHandler;

    /**
     * 加载目录
     *
     * @param string $dir 目录路径（可使用别名）
     * @param string $extension 筛选的扩展名
     * @param string $pattern 自定义模式
     *
     * @return static
     */
    public function loadDir($dir, $extension = null, $pattern = null)
    {
        $dir = I::getAlias($dir);
        if (null === $pattern) {
            if (null === $extension) {
                $this->__dirHandler = new \DirectoryIterator($dir);
            } else {
                $this->__dirHandler = new \DirectoryIterator('glob://' . $dir . '/*.' . $extension);
            }
        } else {
            $this->__dirHandler = new \DirectoryIterator('glob://' . $dir . '/' . $pattern);
        }

        return $this;
    }

    /**
     * 标准的迭代器
     *
     * @return \DirectoryIterator
     */
    public function directoryIterator()
    {
        return $this->__dirHandler;
    }
}
