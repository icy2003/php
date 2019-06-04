<?php
/**
 * Class LocalFile
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2019, icy2003
 */
namespace icy2003\php\ihelpers\file;

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
        $iterator = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
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
    public function createFile($file, $mode = 0777)
    {
        return $this->getIsFile($file) ||
        $this->createDir($this->getDirname($file), $mode) &&
        touch($file) &&
        $this->chmod($file, $mode, FileConstants::RECURSIVE_DISABLED);
    }

    /**
     * @ignore
     */
    public function createFileFromString($file, $string, $mode = 0777)
    {
        return $this->createDir($this->getDirname($file), $mode) &&
        file_put_contents($file, $string) &&
        $this->chmod($file, $mode, FileConstants::RECURSIVE_DISABLED);
    }

    /**
     * @ignore
     */
    public function createDir($dir, $mode = 0777)
    {
        return $this->getIsDir($dir) ||
        $this->createDir($this->getDirname($dir), $mode) &&
        mkdir($dir, $mode);
    }

    /**
     * @ignore
     */
    public function deleteFile($file)
    {
        if ($this->getIsFile($file)) {
            $this->chmod($file, 0777, FileConstants::RECURSIVE_DISABLED);
            return unlink($file);
        }
        return true;
    }

    /**
     * @ignore
     */
    public function deleteDir($dir, $deleteRoot = true)
    {
        if (false === $this->getIsDir($dir)) {
            return true;
        }
        $files = $this->getLists($dir, FileConstants::COMPLETE_PATH);
        foreach ($files as $file) {
            $this->getIsDir($file) ? $this->deleteDir($file) : $this->deleteFile($file);
        }

        return true === $deleteRoot ? rmdir($dir) : true;
    }

    /**
     * @ignore
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
        $this->createDir($this->getDirname($toFile));
        copy($fromFile, $toFile);

        return true;
    }

    /**
     * @ignore
     */
    public function copyDir($fromDir, $toDir, $overWrite = false)
    {
        $fromDir = rtrim($fromDir, '/') . '/';
        $toDir = rtrim($toDir, '/') . '/';
        if (false === $this->getIsDir($fromDir)) {
            return false;
        }
        $this->createDir($toDir);
        $files = $this->getLists($fromDir, FileConstants::COMPLETE_PATH_DISABLED);
        foreach ($files as $file) {
            if ($this->getIsDir($fromDir . $file)) {
                $this->copyDir($fromDir . $file, $toDir . $file, $overWrite);
            } else {
                $this->copyFile($fromDir . $file, $toDir . $file, $overWrite);
            }
        }
        return true;
    }

    /**
     * @ignore
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
        rename($fromFile, $toFile);

        return true;
    }

    /**
     * @ignore
     */
    public function moveDir($fromDir, $toDir, $overWrite = false)
    {
        $fromDir = rtrim($fromDir, '/') . '/';
        $toDir = rtrim($toDir, '/') . '/';
        if (false === $this->getIsDir($fromDir)) {
            return false;
        }
        $files = $this->getLists($fromDir, FileConstants::COMPLETE_PATH_DISABLED);
        foreach ($files as $file) {
            if ($this->getIsDir($fromDir . $file)) {
                $this->moveDir($fromDir . $file, $toDir . $file, $overWrite);
            } else {
                $this->moveFile($fromDir . $file, $toDir . $file, $overWrite);
            }
        }
        return $this->deleteDir($fromDir);
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
    public function close()
    {
        return true;
    }

}
