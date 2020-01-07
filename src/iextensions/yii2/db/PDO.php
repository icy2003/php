<?php
/**
 * Class PDO
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\iextensions\yii2\db;

use icy2003\php\ihelpers\Strings;

/**
 * POD 扩展
 *
 * 添加以 i 开头的新的dsn，用于扩展 yii2
 */
class PDO extends \PDO
{

    /**
     * 当前类名
     *
     * @return string
     */
    public static function className()
    {
        return get_called_class();
    }

    /**
     * 构造函数
     *
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array $attributes
     */
    public function __construct($dsn, $username, $password, $attributes)
    {
        return parent::__construct('mysql' . Strings::partAfter($dsn, 'mysql'), $username, $password, $attributes);
    }
}
