<?php

namespace icy2003\php\iexts\yii2\db;

use icy2003\php\iexts\yii2\db\mysql\Schema;
use icy2003\php\iexts\yii2\db\PDO;
use yii\db\Connection as C;

/**
 * Ê¾Àı db ÅäÖÃ£º
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
        $this->commandMap['imysql'] = Command::className();
    }

    public function createPdoInstance()
    {
        $driver = $this->getDriverName();
        if (in_array($driver, ['imysql'])) {
            $pdoClass = PDO::className();
            return new $pdoClass($this->dsn, $this->username, $this->password, $this->attributes);
        } else {
            return parent::createPdoInstance();
        }
    }
}
