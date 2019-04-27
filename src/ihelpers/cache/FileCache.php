<?php

namespace icy2003\php\ihelpers\cache;

use icy2003\php\I;
use icy2003\php\ihelpers\File;

class FileCache extends Base
{

    protected $_pathRoot;
    protected $_directoryLevel = 1;
    protected $_cacheFileSuffix = '.bin';
    protected $_dirMode = 0775;
    protected $_fileMode;

    public function __construct($pathRoot = null)
    {
        $this->_pathRoot = I::getAlias('@icy2003/php_runtime/cache');
        null !== $pathRoot && $this->_pathRoot = $pathRoot;
    }

    public function set($key, $value, $duration = 0)
    {
        $value = serialize($value);
        $key = $this->buildKey($key);
        return $this->setValue($key, $value, $duration);
    }

    public function buildKey($key)
    {
        return $this->_keyPrefix . md5($key);
    }

    public function setValue($key, $value, $duration = 0)
    {
        $cacheFile = $this->_getCacheFile($key);
        $dirname = File::dirname($cacheFile);
        File::createDir($dirname, $this->_dirMode);
        if (false !== file_put_contents($cacheFile, $value, LOCK_EX)) {
            null !== $this->_fileMode && chmod($cacheFile, $this->_fileMode);
            0 >= $duration && $duration = 3600 * 24 * 365 * 10;
            return touch($cacheFile, $duration + time());
        } else {
            return false;
        }
    }

    protected function _getCacheFile($key)
    {
        if ($this->_directoryLevel > 0) {
            $base = $this->_pathRoot;
            for ($i = 0; $i < $this->_directoryLevel; ++$i) {
                if (false !== ($prefix = substr($key, $i + $i, 2))) {
                    $base .= DIRECTORY_SEPARATOR . $prefix;
                }
            }

            return $base . DIRECTORY_SEPARATOR . $key . $this->_cacheFileSuffix;
        } else {
            return $this->_pathRoot . DIRECTORY_SEPARATOR . $key . $this->_cacheFileSuffix;
        }
    }

    public function get($key)
    {}

    public function delete($key)
    {}

    public function clear()
    {}
}
