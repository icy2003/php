<?php

namespace icy2003\ihelpers;

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

    public static function ip($ip)
    {
        return preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $ip);
    }
}
