<?php
/**
 * Class Command
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\iextensions\yii2\db;

use icy2003\php\ihelpers\Arrays;
use yii\db\Command as DbCommand;

/**
 * Command 扩展
 */
class Command extends DbCommand
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

    /**
     * 批量添加数据
     *
     * @param string $table
     * @param array $rows 键值对二维数组
     *
     * @return integer
     */
    public function inserts($table, $rows)
    {
        $columns = array_keys(Arrays::first($rows));
        return $this->batchInsert($table, $columns, $rows);
    }
}
