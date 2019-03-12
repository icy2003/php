<?php

namespace icy2003\php\ihelpers;

use Exception;
use icy2003\BaseI;

/**
 * 文件 IO 类
 * FileIO::create($fileName) 文件操作的单例对象，可用方法：lines、line、data、getAttribute、getAttributes
 * FileIO::create[delete|copy|move]File[Dir] 静态方法.
 *
 * @todo create 单例会有两次请求，一次获取文件属性，一次用于读取文件
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

    protected static $_instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
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
     * 加载一个文件
     *
     * @param string $fileName
     * @return static
     */
    public function loadFile($fileName)
    {
        $fileName = BaseI::getAlias($fileName);
        if (preg_match('/^https?:\/\//', $fileName)) {
            if (Env::hasExt('curl')) {
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
                $path = Env::value($url, 'path', '/');
                $port = Env::value($url, 'port', 80);
                $fp = fsockopen($host, $port, $errno, $error);
                if ($fp) {
                    $header = [
                        "GET {$path} HTTP/1.0",
                        "HOST: {$host}:{$port}",
                        'Connection: Close',
                    ];
                    fwrite($fp, implode('\r\n', $header) . "\r\n\r\n");
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
            if ($this->__attributes['isExists'] = file_exists($fileName)) {
                $this->__attributes['fileSize'] = filesize($fileName);
                $this->__attributes['fileType'] = filetype($fileName);
            }
        }
        $this->__attributes['fileName'] = Env::iBaseName($fileName);
        $this->__attributes['filePath'] = $fileName;
        if (!$this->__attributes['isExists']) {
            throw new Exception("文件 {$fileName} 不存在");
        }
        if (!$this->__fileHandler = fopen($fileName, 'rb')) {
            throw new Exception('无法打开文件');
        }
        return $this;
    }

    private $__fileHandler;
    private $__attributes = [
        'isExists' => false,
        'fileSize' => 0,
        'fileType' => '',
        'fileName' => '',
        'filePath' => '',
    ];

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
        return Env::value($this->__attributes, $name, $defaultValue);
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
     * 遍历行的生成器
     *
     * @return \Generator
     */
    public function lines()
    {
        try {
            while ($line = fgets($this->__fileHandler)) {
                yield $line;
            }
        } finally {
            fclose($this->__fileHandler);
        }
    }

    /**
     * 返回文本的某行，从 0 行开始
     *
     * @param int $num 默认值
     *
     * @return string
     */
    public function line($num = 0)
    {
        foreach ($this->lines() as $k => $line) {
            if ($k == $num) {
                return $line;
            }
        }
    }

    /**
     * 遍历字节的生成器
     *
     * @param int $buffer 缓冲区长度，默认 1024
     *
     * @return \Generator
     */
    public function data($buffer = 1024)
    {
        try {
            while (!feof($this->__fileHandler) && $this->__attributes['fileSize'] > $bufferSize) {
                $bufferSize += $buffer;
                yield fread($this->__fileHandler, $buffer);
            }
        } finally {
            fclose($this->__fileHandler);
        }
    }

    /**
     *
     * @var \DirectoryIterator
     */
    private $__dirHandler;
    private $__dir;

    /**
     * 目录的迭代器
     *
     * @param string $dir 目录路径（可使用别名）
     * @param string $extension 筛选的扩展名
     * @param string $pattern 自定义模式
     *
     * @return static
     */
    public function loadDir($dir, $extension = null, $pattern = null)
    {
        $this->__dir = BaseI::getAlias($dir);
        if (null === $pattern) {
            if (null === $extension) {
                $this->__dirHandler = new \DirectoryIterator($this->__dir);
            } else {
                $this->__dirHandler = new \DirectoryIterator("glob://{$this->__dir}/*.{$extension}");
            }
        } else {
            $this->__dirHandler = new \DirectoryIterator("glob://{$this->__dir}/{$pattern}");
        }

        return $this;
    }

    /**
     * 标准的迭代器
     *
     * @return \DirectoryIterator
     */
    public function spl()
    {
        return $this->__dirHandler;
    }

    /**
     * @deprecated 使用标准的迭代器代替，即将废弃
     *
     * @return \Generator
     */
    public function filePath()
    {
        yield $this->spl()->getPathname();
    }
}
