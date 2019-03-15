<?php

namespace icy2003\php\iexts\yii2\db;

/**
 * 添加以 i 开头的新的dsn，用于扩展 yii2
 */
class PDO extends \PDO
{

    public static function className()
    {
        return get_called_class();
    }

    public function __construct($dsn, $username, $password, $attributes)
    {
        return parent::__construct(substr($dsn, 1), $username, $password, $attributes);
    }
}
