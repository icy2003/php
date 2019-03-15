<?php

namespace icy2003\php\iexts\yii2\db;

use icy2003\php\iexts\yii2\db\mysql\Schema;
use yii\db\Connection as C;

/**
 * 示例 db 配置：
 * [
 *     'class' => Connection::className(),
 *     'dsn' => 'imysql:host=127.0.0.1;dbname=test',
 * ]
 */
class Connection extends C
{

    public function init()
    {
        $this->schemaMap['imysql'] = Schema::className();
        $driver = $this->getDriverName();
        if (in_array($driver, ['imysql'])) {
            $count = 1;
            $realDriver = substr($driver, 1);
            $this->dsn = str_replace($driver, $realDriver, $this->dsn, $count);
            $this->setDriverName($realDriver);
        }
    }
}
