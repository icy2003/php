<?php
/**
 * Class C
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php;

use Exception;

/**
 * 配置（Config）、检查（Check）、常量（Constant）
 */
class C
{
    /**
     * 断言扩展，未加载则抛错
     *
     * @param string $extension 扩展名
     * @param string $message
     *
     * @return void
     */
    public static function assertExtension($extension, $message)
    {
        if (false === extension_loaded($extension)) {
            throw new Exception($message);
        }
    }

    /**
     * 断言函数，不存在则抛错
     *
     * @param string $function
     * @param string $message
     *
     * @return void
     */
    public static function assertFunction($function, $message)
    {
        if (false === function_exists($function)) {
            throw new Exception($message);
        }
    }

    /**
     * 断言真，不为真则抛错
     *
     * @param boolean $isTrue
     * @param string $message
     *
     * @return void
     */
    public static function assertTrue($isTrue, $message)
    {
        if (true !== $isTrue) {
            throw new Exception($message);
        }
    }

    /**
     * 断言非真，为真则抛错
     *
     * @param boolean $isTrue
     * @param string $message
     *
     * @return void
     */
    public static function assertNotTrue($isNotTrue, $message)
    {
        if (true === $isNotTrue) {
            throw new Exception($message);
        }
    }

    /**
     * 断言假，不为假则抛错
     *
     * @param boolean $isTrue
     * @param string $message
     *
     * @return void
     */
    public static function assertFalse($isFalse, $message)
    {
        if (false !== $isFalse) {
            throw new Exception($message);
        }
    }

    /**
     * 断言非假，为假则抛错
     *
     * @param boolean $isTrue
     * @param string $message
     *
     * @return void
     */
    public static function assertNotFalse($isNotFalse, $message)
    {
        if (false === $isNotFalse) {
            throw new Exception($message);
        }
    }
}
