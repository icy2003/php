<?php
/**
 * Class Connection
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\iexts\yii2\db;

use icy2003\php\iexts\yii2\db\mysql\Schema;
use icy2003\php\iexts\yii2\db\PDO;
use yii\db\Connection as C;

/**
 * Connection 扩展
 *
 * 示例 db 配置：
 * [
 *     'class' => Connection::className(),
 *     'dsn' => 'imysql:host=127.0.0.1;dbname=test',
 * ]
 */
class Connection extends C
{

    /**
     * 初始化
     *
     * @return void
     */
    public function init()
    {
        $this->schemaMap['imysql'] = Schema::className();
        $this->commandMap['imysql'] = Command::className();
    }

    /**
     * 创建 PDO 对象
     *
     * @return \PDO
     */
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

    /**
     * 初始化
     */
    protected function initConnection()
    {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($this->emulatePrepare !== null && constant('PDO::ATTR_EMULATE_PREPARES')) {
            if ($this->driverName !== 'sqlsrv') {
                $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, $this->emulatePrepare);
            }
        }
        if ($this->charset !== null && in_array($this->getDriverName(), ['pgsql', 'mysql', 'mysqli', 'cubrid', 'imysql'], true)) {
            $this->pdo->exec('SET NAMES ' . $this->pdo->quote($this->charset));
        }
        $this->trigger(self::EVENT_AFTER_OPEN);
    }
}
