<?php
/**
 * Class LocalFile
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2019, icy2003
 */
namespace icy2003\php\icomponents\file;

use Exception;
use icy2003\php\C;
use icy2003\php\I;
use icy2003\php\icomponents\file\FileInterface;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Charset;
use icy2003\php\ihelpers\Header;
use icy2003\php\ihelpers\Request;
use icy2003\php\ihelpers\Strings;

/**
 * 本地文件
 *
 * - 支持本地文件操作
 * - 支持网络文件：文件是否存在、文件大小
 * - 下载文件请使用 download() 而不是 downloadFile()
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
        $this->_functions['loader'] = function ($fileName, $mode = 'rb') {
            $hashName = $this->__hash($fileName);
            $this->_attributes[$hashName] = I::get($this->_attributes, $hashName, [
                'isCached' => false,
                'isLocal' => true,
                'file' => $this->__file($fileName),
                // 以下属性需要重新设置
                'isExists' => false,
                'fileSize' => 0,
                'spl' => null,
                'splInfo' => null,
            ]);
            try {
                $this->chmod($this->_attributes[$hashName]['file']);
                null === $this->_attributes[$hashName]['spl'] && $this->_attributes[$hashName]['spl'] = new \SplFileObject($this->__file($fileName), $mode);
                null === $this->_attributes[$hashName]['splInfo'] && $this->_attributes[$hashName]['splInfo'] = new \SplFileInfo($this->__file($fileName));
            } catch (Exception $e) {

            }
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
                    // 公用名(Common Name)一般来讲就是填写你将要申请SSL证书的域名 (domain)或子域名(sub domain)
                    // - 设置为 1 是检查服务器SSL证书中是否存在一个公用名(common name)
                    // - 设置成 2，会检查公用名是否存在，并且是否与提供的主机名匹配
                    // - 0 为不检查名称。 在生产环境中，这个值应该是 2（默认值）
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                    // 禁止 cURL 验证对等证书（peer's certificate）。要验证的交换证书可以在 CURLOPT_CAINFO 选项中设置
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                    $result = curl_exec($curl);
                    if ($result && $info = curl_getinfo($curl)) {
                        if (200 == $info['http_code']) {
                            $this->_attributes[$hashName]['isExists'] = true;
                            $this->_attributes[$hashName]['fileSize'] = $info['download_content_length'];
                        }
                    }
                    curl_close($curl);
                } elseif ((bool) ini_get('allow_url_fopen')) {
                    $headArray = (array) get_headers($fileName, 1);
                    if (preg_match('/200/', $headArray[0])) {
                        $this->_attributes[$hashName]['isExists'] = true;
                        $this->_attributes[$hashName]['fileSize'] = $headArray['Content-Length'];
                    }
                } else {
                    $url = parse_url($fileName);
                    $host = $url['host'];
                    $path = (string) I::get($url, 'path', '/');
                    $port = (int) I::get($url, 'port', 80);
                    $fp = fsockopen($host, $port);
                    if (is_resource($fp)) {
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
                $this->_attributes[$hashName]['isExists'] = file_exists($this->_attributes[$hashName]['file']);
                if ($this->_attributes[$hashName]['isExists']) {
                    $this->_attributes[$hashName]['fileSize'] = filesize($this->_attributes[$hashName]['file']);
                }
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
    private function __hash($fileName)
    {
        return md5($fileName);
    }

    /**
     * 返回路径别名
     *
     * @param string $file
     *
     * @return string
     */
    private function __file($file)
    {
        return (string) I::getAlias($file);
    }

    /**
     * 获取网络文件的属性
     *
     * @param string $fileName
     * @param string $name
     * @param string $mode 读写的模式，默认 rb
     *
     * @return mixed
     */
    public function attribute($fileName, $name, $mode = 'rb')
    {
        I::call($this->_functions['loader'], [$fileName, $mode]);
        return I::get($this->_attributes, $this->__hash($fileName) . '.' . $name);
    }

    /**
     * 获取文件对象
     *
     * @param string $fileName
     * @param string $mode 读写的模式，默认 rb
     *
     * @return \SplFileObject
     */
    public function spl($fileName, $mode = 'rb')
    {
        $spl = $this->attribute($fileName, 'spl', $mode);
        if ($spl instanceof \SplFileObject) {
            return $spl;
        }
        throw new Exception('文件打开失败：' . $fileName);
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
        $splInfo = $this->attribute($fileName, 'splInfo');
        if ($splInfo instanceof \SplFileInfo) {
            return $splInfo;
        }
        throw new Exception('文件打开失败：' . $fileName);
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
            while (false === $spl->eof() && $line = $spl->fgets()) {
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
     * @return string|null
     */
    public function line($fileName, $num = 0, $autoClose = false)
    {
        $spl = $this->spl($fileName);
        foreach ($this->linesGenerator($fileName, $autoClose) as $k => $line) {
            if ($k == $num) {
                $spl->rewind();
                return $line;
            }
        }
        return null;
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
            $spl = $this->spl($fileName);
            $size = $this->getFilesize($fileName);
            while (!$spl->eof() && $size > $bufferSize) {
                $bufferSize += $buffer;
                yield $spl->fread($bufferSize);
            }
        } finally {
            true === $autoClose && $this->close($fileName);
        }
    }

    /**
     * @ignore
     */
    public function getATime($fileName)
    {
        return fileatime($this->__file($fileName));
    }

    /**
     * @ignore
     */
    public function getBasename($path, $suffix = null)
    {
        return parent::getBasename($this->__file($path), $suffix);
    }

    /**
     * @ignore
     */
    public function getCTime($fileName)
    {
        return filectime($this->__file($fileName));
    }

    /**
     * @ignore
     */
    public function getExtension($fileName)
    {
        return pathinfo($this->__file($fileName), PATHINFO_EXTENSION);
    }

    /**
     * @ignore
     */
    public function getFilename($fileName)
    {
        return pathinfo($this->__file($fileName), PATHINFO_FILENAME);
    }

    /**
     * @ignore
     */
    public function getMtime($fileName)
    {
        return filemtime($this->__file($fileName));
    }

    /**
     * @ignore
     */
    public function getDirname($path)
    {
        return parent::getDirname($this->__file($path));
    }

    /**
     * @ignore
     */
    public function getPerms($path)
    {
        return fileperms($this->__file($path));
    }

    /**
     * @ignore
     */
    public function getFilesize($fileName)
    {
        return (int) $this->attribute($fileName, 'fileSize');
    }

    /**
     * @ignore
     */
    public function getType($path)
    {
        return filetype($this->__file($path));
    }

    /**
     * @ignore
     */
    public function isDir($dir)
    {
        return is_dir($this->__file($dir));
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
        return (bool) $this->attribute($file, 'isExists');
    }

    /**
     * @ignore
     */
    public function isLink($link)
    {
        return is_link($this->__file($link));
    }

    /**
     * @ignore
     */
    public function isReadable($path)
    {
        return is_readable($this->__file($path));
    }

    /**
     * @ignore
     */
    public function isWritable($path)
    {
        return is_writable($this->__file($path));
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
        $file = realpath($this->__file($path));
        if (false === $file) {
            $file = $path;
        }
        return $file;
    }

    /**
     * @ignore
     */
    public function getLists($dir = null, $flags = FileConstants::COMPLETE_PATH)
    {
        null === $dir && $dir = $this->getRealpath('./');
        $dir = $this->__file(rtrim($dir, '/') . '/');
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
        return file_get_contents($this->__file($file));
    }

    /**
     * @ignore
     */
    public function putFileContent($file, $string, $mode = 0777)
    {
        $file = $this->__file($file);
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
        $file = $this->__file($file);
        if ($this->isFile($file)) {
            $this->close($file);
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
     * - 从远程下载文件到本地
     * - @param callback|null $callback 执行中的回调
     *
     * @ignore
     */
    public function downloadFile($fromFile, $toFile = null, $overwrite = true, $callback = null)
    {
        if (null === $toFile) {
            list($fromFile, $toFile) = $this->fileMap($fromFile);
        } else {
            list($fromFile) = $this->fileMap($fromFile);
        }
        if ($this->isFile($toFile) && false === $overwrite) {
            return true;
        }
        $fromSpl = $this->spl($fromFile, 'rb');
        $toSpl = $this->spl($toFile, 'wb');
        $size = 0;
        $total = $this->getFilesize($fromFile);
        while (false === $fromSpl->eof()) {
            $out = $fromSpl->fread(1024 * 8);
            $toSpl->fwrite($out);
            $size += Strings::byteLength($out);
            I::call($callback, [$size, $total]);
        }
        $this->close([$fromFile, $toFile]);
    }

    /**
     * download() 配置名：ip
     */
    const C_DOWNLOAD_IP = 'ip';
    /**
     * download() 配置名：speed
     */
    const C_DOWNLOAD_SPEED = 'speed';
    /**
     * download() 配置名：xSendFile
     */
    const C_DOWNLOAD_X_SEND_FILE = 'xSendFile';
    /**
     * download() 配置名：xSendFileRoot
     */
    const C_DOWNLOAD_X_SEND_FILE_ROOT = 'xSendFileRoot';

    /**
     * 服务端给客户端提供下载请求
     *
     * @param string|array $fileName 如果是数组，第一个元素是原名，第二个元素为下载名，原名需要指定路径，下载名不需要
     * @param null|array $config 配置项
     *      - ip：限特定 IP 访问，数组或逗号字符串，默认为 *，即对所有 IP 不限制
     *      - speed：限速，默认不限速，单位 kb/s
     *      - xSendFile：是否使用 X-Sendfile 进行下载，默认 false，即不使用。X-Sendfile 缓解了 PHP 的压力，但同时 PHP 将失去对资源的控制权，因为 PHP 并不知道资源发完了没
     *      - xSendFileRoot：文件根路径，默认为 /protected/。此时 Nginx 可作如下配置，更多 @link https://www.nginx.com/resources/wiki/start/topics/examples/xsendfile/
     *      ```nginx.conf
     *      location /protected/ {
     *          internal; # 表示这个路径只能在 Nginx 内部访问，不能用浏览器直接访问防止未授权的下载
     *          alias   /usr/share/nginx/html/protected/; # 别名
     *          # root    /usr/share/nginx/html; # 根目录
     *      }
     *      ```
     * @param callback $callback 下载完成后的回调，参数列表：文件属性数组
     *
     * @return void
     * @throws Exception
     */
    public function download($fileName, $config = null, $callback = null)
    {
        Header::xPoweredBy();
        set_time_limit(0);
        list($originName, $downloadName) = $this->fileMap($fileName);
        $originName = $this->__file($originName);
        try {
            $ip = I::get($config, self::C_DOWNLOAD_IP, '*');
            if ('*' !== $ip) {
                C::assertTrue(Arrays::in((new Request())->getUserIP(), Strings::toArray($ip)), 'http/1.1 403.6 此 IP 禁止访问');
            }
            if ($this->isFile($originName)) {
                $fileSize = $this->getFilesize($originName);
                header('Content-type:application/octet-stream');
                header('Accept-Ranges:bytes');
                header('Content-Length:' . $fileSize);
                header('Content-Disposition: attachment; filename=' . $downloadName);
                $speed = I::get($config, self::C_DOWNLOAD_SPEED, 0);
                $xSendFile = I::get($config, self::C_DOWNLOAD_X_SEND_FILE, false);
                $xSendFileRoot = I::get($config, self::C_DOWNLOAD_X_SEND_FILE_ROOT, '/protected/');
                if (true === $xSendFile) {
                    $path = rtrim($xSendFileRoot, '/') . '/' . $this->getBasename($originName);
                    header('X-Accel-Redirect: ' . $path); // Nginx、Cherokee 实现了该头
                    header('X-Sendfile: ' . $path); // Apache、Lighttpd v1.5、Cherokee 实现了该头
                    header('X-LIGHTTPD-send-file: ', $path); // Lighttpd v1.4 实现了该头
                    if ($speed) {
                        header('X-Accel-Limit-Rate: ' . $speed); // 单位 kb/s
                    }
                } else {
                    flush();
                    foreach ($this->dataGenerator($originName, true, ($speed ? $speed : 1024) * 1024) as $data) {
                        echo $data;
                        flush();
                        $speed > 0 && sleep(1);
                    }
                }
            }
        } catch (Exception $e) {
            header($e->getMessage());
        } finally {
            I::call($callback, [$this->_attributes]);
            // 必须要终止掉，防止发送其他数据导致错误
            die;
        }
    }

    /**
     * 返回文件映射
     *
     * @param string|array $file 支持别名
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

    /**
     * @ignore
     */
    public function chown($file, $user, $flags = FileConstants::RECURSIVE_DISABLED)
    {
        $file = $this->__file($file);
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
        $file = $this->__file($file);
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
        $file = $this->__file($file);
        if ($this->isDir($file) && I::hasFlag($flags, FileConstants::RECURSIVE)) {
            $files = $this->getLists($file, FileConstants::COMPLETE_PATH | FileConstants::RECURSIVE);
            foreach ($files as $subFile) {
                @chmod($subFile, $mode);
            }
        }
        return (bool) @chmod($file, $mode);
    }

    /**
     * @ignore
     */
    public function symlink($from, $to)
    {
        $from = $this->__file($from);
        $to = $this->__file($to);
        return symlink($from, $to);
    }

    /**
     * @ignore
     */
    public function close($fileName = null)
    {
        if (is_string($fileName)) {
            $fileName = [$this->__hash($fileName)];
        } elseif (is_array($fileName)) {
            foreach ($fileName as $k => $name) {
                $fileName[$k] = $this->__hash($name);
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
        $fromFile = $this->__file($fromFile);
        $toFile = $this->__file($toFile);
        return copy($fromFile, $toFile);
    }

    /**
     * @ignore
     */
    protected function _move($fromFile, $toFile)
    {
        $fromFile = $this->__file($fromFile);
        $toFile = $this->__file($toFile);
        return rename($fromFile, $toFile);
    }

    /**
     * @ignore
     */
    protected function _mkdir($dir, $mode = 0777)
    {
        $dir = $this->__file($dir);
        return mkdir($dir, $mode);
    }

    /**
     * @ignore
     */
    protected function _rmdir($dir)
    {
        $dir = $this->__file($dir);
        return rmdir($dir);
    }

}
