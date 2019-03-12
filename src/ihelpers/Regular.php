<?php

namespace icy2003\php\ihelpers;

/**
 * 一些常用的正则表达式校验
 */
class Regular
{

    /**
     * 验证邮箱
     * @see http://www.regular-expressions.info/email.html
     *
     * @param string $email
     *
     * @return boolean
     */
    public static function email($email)
    {
        return preg_match('/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/', $email);
    }

    /**
     * 验证 IP
     * @see https://www.regular-expressions.info/ip.html
     *
     * @param string $ip
     *
     * @return boolean
     */
    public static function ip($ip)
    {
        return preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $ip);
    }

    /**
     * 验证手机
     *
     * @param string $mobile
     *
     * @return boolean
     */
    public static function mobile($mobile)
    {
        return preg_match('/^1[3-9][0-9]\d{8}$/', $mobile);
    }

    /**
     * 验证身份证号
     *
     * @param string $idCard
     *
     * @return boolean
     */
    public static function idCard($idCard)
    {
        return preg_match('/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}$)/', $idCard);
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
        return preg_match('/^https?:\/\//', $url);
    }
}
