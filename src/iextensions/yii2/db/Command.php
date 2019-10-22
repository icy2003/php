<?php
/**
 * Class Command
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\iextensions\yii2\db;

use yii\db\Command as C;

/**
 * Command 扩展
 */
class Command extends C
{
    /**
     * 判断表是否存在
     *
     * @param string $table
     *
     * @return boolean
     */
    public function tableExists($table)
    {
        $tables = $this->db->getSchema()->getTableNames();
        return in_array($this->db->tablePrefix . $table, $tables);
    }
}
