<?php
/**
 * Class Preg
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

/**
 * 正则
 */
class Preg
{
    /**
     * 关闭 JIT
     *
     * @param boolean $isOn 是否开启，默认 false
     *
     * @return void
     */
    public static function jitOff($isOn = false)
    {
        /**
         * @see /samples/php7preg_bug.php
         */
        ini_set('pcre.jit', true === $isOn ? 1 : 0);
    }

    /**
     * 判断一个正则是否合法
     *
     * @param string $pattern 正则表达式
     *
     * @return boolean
     */
    public static function isLegal($pattern)
    {
        return false !== @preg_match($pattern, null);
    }
}
