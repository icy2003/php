<?php
/**
 * Class FileCache
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers\cache;

use icy2003\php\I;
use icy2003\php\ihelpers\File;

/**
 * 文件缓存
 */
class FileCache extends Base
{

    /**
     * 文件缓存根目录
     *
     * @var string
     */
    protected $_pathRoot;
    /**
     * 文件缓存目录层级
     *
     * @var int
     */
    protected $_directoryLevel = 1;
    /**
     * 缓存后缀
     *
     * @var string
     */
    protected $_cacheFileSuffix = '.bin';
    /**
     * 目录权限
     *
     * @var int
     */
    protected $_dirMode = 0775;
    /**
     * 文件权限
     *
     * @var int
     */
    protected $_fileMode;

    /**
     * 构造函数
     *
     * @param string $pathRoot 缓存根目录
     */
    public function __construct($pathRoot = null)
    {
        if (null === $pathRoot) {
            $this->_pathRoot = I::getAlias('@icy2003/php_runtime/cache');
        } else {
            $this->_pathRoot = $pathRoot;
        }
        File::fileExists($this->_pathRoot) || File::createDir($this->_pathRoot);
    }

    /**
     * 设置一个缓存
     *
     * @param string $key 缓存键
     * @param mixed $value 缓存值
     * @param int $duration 有效期，默认 0，表示永久
     *
     * @return void
     */
    public function set($key, $value, $duration = 0)
    {
        $value = serialize($value);
        $fullKey = $this->buildKey($key);
        return $this->_setValue($fullKey, $value, $duration);
    }

    /**
     * 获取一个缓存
     *
     * @param string $key 缓存键
     *
     * @return mixed
     */
    public function get($key)
    {
        $fullKey = $this->buildKey($key);
        $value = $this->_getValue($fullKey);
        return false === $value ? false : unserialize($value);
    }

    /**
     * 删除一个缓存
     *
     * @param string $key 缓存键
     *
     * @return void
     */
    public function delete($key)
    {
        $fullKey = $this->buildKey($key);
        return $this->_deleteValue($fullKey);
    }

    /**
     * 创建一个 key
     *
     * @param string $key
     *
     * @return string
     */
    public function buildKey($key)
    {
        return $this->_keyPrefix . md5($key);
    }

    /**
     * 设置一个缓存
     *
     * @param string $fullKey 缓存键全名
     * @param mixed $value 缓存值
     * @param int $duration 有效期，默认 0，表示永久
     *
     * @return void
     */
    protected function _setValue($fullKey, $value, $duration = 0)
    {
        $cacheFile = $this->_getCacheFile($fullKey);
        $dirname = File::dirname($cacheFile);
        File::createDir($dirname, $this->_dirMode);
        if (false !== file_put_contents($cacheFile, $value, LOCK_EX)) {
            null !== $this->_fileMode && @chmod($cacheFile, $this->_fileMode);
            0 >= $duration && $duration = 3600 * 24 * 365 * 10;
            return @touch($cacheFile, $duration + time());
        } else {
            return false;
        }
    }

    /**
     * 获取一个缓存
     *
     * @param string $fullKey 缓存键全名
     *
     * @return mixed
     */
    protected function _getValue($fullKey)
    {
        $cacheFile = $this->_getCacheFile($fullKey);
        if (File::fileExists($cacheFile)) {
            if (filemtime($cacheFile) > time()) {
                return file_get_contents($cacheFile);
            }
            $this->_deleteValue($fullKey);
        }
        return false;
    }

    /**
     * 删除一个缓存
     *
     * @param string $fullKey 缓存键全名
     *
     * @return void
     */
    protected function _deleteValue($fullKey)
    {
        $cacheFile = $this->_getCacheFile($fullKey);
        return File::deleteFile($cacheFile);
    }

    /**
     * 获取缓存文件
     *
     * @param string $fullKey 缓存键全名
     *
     * @return string
     */
    protected function _getCacheFile($fullKey)
    {
        if ($this->_directoryLevel > 0) {
            $base = $this->_pathRoot;
            for ($i = 0; $i < $this->_directoryLevel; ++$i) {
                if (false !== ($prefix = substr($fullKey, $i + $i, 2))) {
                    $base .= DIRECTORY_SEPARATOR . $prefix;
                }
            }

            return $base . DIRECTORY_SEPARATOR . $fullKey . $this->_cacheFileSuffix;
        } else {
            return $this->_pathRoot . DIRECTORY_SEPARATOR . $fullKey . $this->_cacheFileSuffix;
        }
    }

    /**
     * 清空缓存
     *
     * @return void
     */
    public function clear()
    {
        return File::deleteDir($this->_pathRoot, false);
    }
}
