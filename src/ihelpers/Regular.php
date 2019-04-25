<?php
/**
 * Class Regular
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\ihelpers;

/**
 * 正则相关
 *
 * 常用的正则表达式校验
 */
class Regular
{

    /**
     * 验证邮箱
     *
     * @see http://www.regular-expressions.info/email.html
     *
     * @param string $email 邮箱
     *
     * @return boolean
     */
    public static function email($email)
    {
        return (bool)preg_match('/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/', $email);
    }

    /**
     * 验证 IP
     *
     * @see https://www.regular-expressions.info/ip.html
     *
     * @param string $ip IP
     *
     * @return boolean
     */
    public static function ip($ip)
    {
        return (bool)preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $ip);
    }

    /**
     * 验证手机
     *
     * @param string $mobile 手机号
     *
     * @return boolean
     */
    public static function mobile($mobile)
    {
        return (bool)preg_match('/^1[3-9][0-9]\d{8}$/', $mobile);
    }

    /**
     * 验证身份证号
     *
     * @param string $idCard 身份证号
     *
     * @return boolean
     */
    public static function idCard($idCard)
    {
        return (bool)preg_match('/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}$)/', $idCard);
    }

    /**
     * 验证 URL
     *
     * @param string $url
     *
     * @return boolean
     */
    public static function url($url)
    {
        return (bool)preg_match('/^https?:\/\//', $url);
    }

    /**
     * 判断是否是中文
     *
     * @param string $chinese 中文字符
     *
     * @return boolean
     */
    public static function isChinese($chinese)
    {
        $chinese = Charset::toUtf($chinese);
        return (bool)preg_match('/[\x{4e00}-\x{9fa5}]+/u', $chinese);
    }

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
