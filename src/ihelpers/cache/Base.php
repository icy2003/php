<?php
/**
 * abstract Class Base
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2019, icy2003
 */

namespace icy2003\php\ihelpers\cache;

/**
 * 缓存抽象类
 */
abstract class Base
{

    /**
     * 缓存前缀
     *
     * @var string
     */
    protected $_keyPrefix;

    /**
     * 设置一个缓存
     *
     * @param string $key 缓存键
     * @param mixed $value 缓存值
     * @param int $duration 有效期，默认 0，表示永久
     *
     * @return boolean
     */
    abstract public function set($key, $value, $duration = 0);

    /**
     * 获取一个缓存
     *
     * @param string $key 缓存键
     *
     * @return mixed
     */
    abstract public function get($key);

    /**
     * 删除一个缓存
     *
     * @param string $key 缓存键
     *
     * @return boolean
     */
    abstract public function delete($key);

    /**
     * 清空缓存
     *
     * @return boolean
     */
    abstract public function clear();
}
