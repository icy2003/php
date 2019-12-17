<?php
/**
 * Class Command
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iextensions\yii2\db\mysql\workerman;

use icy2003\php\iextensions\yii2\C;
use icy2003\php\iextensions\yii2\db\Command as DbCommand;

/**
 * 用于 workerman 的 Yii2 的 mysql 的 Command 类扩展
 */
class Command extends DbCommand
{
    /**
     * @see \yii\db\Command execute()
     *
     * @return integer 受执行影响的行数
     * @throws 异常执行失败
     */
    public function execute()
    {
        try {
            return parent::execute();
        } catch (\yii\db\Exception $e) {
            if ($e->errorInfo[1] == C::MYSQL_SERVER_HAS_GONE_AWAY || $e->errorInfo[1] == C::MYSQL_LOST_CONNECTION_TO_MYSQL) {
                $this->db->close();
                $this->db->open();
                $this->pdoStatement = null;
                return parent::execute();
            } else {
                throw $e;
            }
        }
    }

    /**
     * @see \yii\db\Command queryInternal()
     *
     * @return mixed 方法执行结果
     * @throws 如果查询导致任何问题，则出现异常
     */
    protected function queryInternal($method, $fetchMode = null)
    {
        try {
            return parent::queryInternal($method, $fetchMode);
        } catch (\yii\db\Exception $e) {
            if ($e->errorInfo[1] == C::MYSQL_SERVER_HAS_GONE_AWAY || $e->errorInfo[1] == C::MYSQL_LOST_CONNECTION_TO_MYSQL) {
                $this->db->close();
                $this->db->open();
                $this->pdoStatement = null;
                return parent::queryInternal($method, $fetchMode);
            } else {
                throw $e;
            }
        }
    }
}
