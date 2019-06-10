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
use Symfony\Component\Process\Process;

/**
 * 本地文件
 */
class LocalFile extends Base
{
    /**
     * @ignore
     */
    public function getCommandResult($command)
    {
        $process = new Process($command);
        $process->run();
        return $process->getOutput();
    }

    /**
     * @ignore
     */
    public function getIsFile($file)
    {
        return file_exists($file);
    }

    /**
     * @ignore
     */
    public function getIsDir($dir)
    {
        return is_dir($dir);
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
    public function getFilesize($file)
    {
        return filesize($file);
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
        if ($this->getIsFile($file)) {
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
        if ($this->getIsDir($file) && I::hasFlag($flags, FileConstants::RECURSIVE)) {
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
        if ($this->getIsDir($file) && I::hasFlag($flags, FileConstants::RECURSIVE)) {
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
        if ($this->getIsDir($file) && I::hasFlag($flags, FileConstants::RECURSIVE)) {
            $files = $this->getLists($file, FileConstants::COMPLETE_PATH | FileConstants::RECURSIV);
            foreach ($files as $subFile) {
                chmod($subFile, $mode);
            }
        }
        return (bool)chmod($file, $mode);
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
    public function close()
    {
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
