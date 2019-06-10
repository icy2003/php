<?php
/**
 * Class ZipFile
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2019, icy2003
 */
namespace icy2003\php\icomponents\file;

use icy2003\php\I;
use icy2003\php\ihelpers\Charset;

/**
 * zip
 */
class ZipFile extends Base
{

    /**
     * @var \ZipArchive zip 句柄
     */
    protected $_zip;

    /**
     * 打开一个 zip 文件
     *
     * @param string $zipFile
     */
    public function __construct($zipFile)
    {
        $this->_zip = @zip_open($zipFile);
        if (false === is_resource($this->_zip)) {
            throw new \Exception("zip 打开错误");
        }
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
    public function getIsFile($file)
    {
        $file = Charset::toUtf(ltrim($file, '/\\'));
        return false !== $this->_zip->getFromName($file);
    }

    /**
     * @ignore
     */
    public function getIsDir($dir)
    {

    }

    /**
     * @ignore
     */
    public function getLists($dir = null, $flags = FileConstants::COMPLETE_PATH)
    {

    }

    /**
     * @ignore
     */
    public function getFilesize($file)
    {

    }

    /**
     * @ignore
     */
    public function getFileContent($file)
    {
        return $this->_zip->getFromName($file);
    }

    /**
     * @ignore
     */
    public function putFileContent($file, $string, $mode = 0777)
    {

    }

    /**
     * @ignore
     */
    public function deleteFile($file)
    {

    }

    /**
     * @ignore
     */
    public function uploadFile($toFile, $fromFile = null, $overwrite = true)
    {

    }

    /**
     * @ignore
     */
    public function downloadFile($fromFile, $toFile = null, $overwrite = true)
    {

    }

    /**
     * @ignore
     */
    public function chown($file, $user, $flags = FileConstants::RECURSIVE_DISABLED)
    {

    }

    /**
     * @ignore
     */
    public function chgrp($file, $group, $flags = FileConstants::RECURSIVE_DISABLED)
    {

    }

    /**
     * @ignore
     */
    public function chmod($file, $mode = 0777, $flags = FileConstants::RECURSIVE_DISABLED)
    {

    }

    /**
     * @ignore
     */
    public function symlink($from, $to)
    {

    }

    /**
     * @ignore
     */
    public function close()
    {
        $this->_zip->close();
    }

    /**
     * @ignore
     */
    protected function _copy($fromFile, $toFile)
    {

    }

    /**
     * @ignore
     */
    protected function _move($fromFile, $toFile)
    {

    }

    /**
     * @ignore
     */
    protected function _mkdir($dir, $mode = 0777)
    {

    }

    /**
     * @ignore
     */
    protected function _rmdir($dir)
    {

    }
}
