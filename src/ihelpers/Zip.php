<?php

namespace icy2003\ihelpers;

use Exception;
use DirectoryIterator;
use ZipArchive;

class Zip
{
    private static $instance;
    private $ziper;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * 创建 zip 类的单例对象
     *
     * @return static
     */
    public static function create()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
            if (Env::hasExt('zip')) {
                self::$instance->ziper = new ZipArchive();
            } else {
                throw new Exception('没有安装 zip 扩展');
            }
        }

        return self::$instance;
    }

    /**
     * 打包一个文件夹.
     *
     * @param string $zipFile zip 文件路径
     * @param string $folder  需要打包的文件目录
     */
    public function zipFolder($zipFile, $folder)
    {
        if (true === $this->ziper->open($zipFile, ZipArchive::CREATE)) {
            $createZip = function ($folder, $path) use (&$createZip) {
                $this->ziper->addEmptyDir($path);
                $dir = new DirectoryIterator($folder);
                foreach ($dir as $file) {
                    if (!$file->isDot()) {
                        $pathName = $file->getPathname();
                        if ($file->isDir()) {
                            $createZip($pathName, $path . '/' . basename($pathName));
                        } else {
                            $this->ziper->addFile($pathName, $path . '/' . basename($pathName));
                        }
                    }
                }
            };
            $createZip($folder, basename($folder));
            $this->ziper->close();
        } else {
            throw new Exception("无法打开 {$zipFile}");
        }
    }
}
