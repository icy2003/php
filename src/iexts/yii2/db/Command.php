<?php

namespace icy2003\php\iexts\yii2\db;

use yii\db\Command as C;

class Command extends C
{
    public function tableExists($table)
    {
        $tables = $this->db->getSchema()->getTableNames();
        return in_array($this->db->tablePrefix . $table, $tables);
    }
}
