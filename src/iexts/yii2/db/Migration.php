<?php

namespace icy2003\php\iexts\yii2\db;

use icy2003\php\I;
use yii\db\Migration as M;

class Migration extends M
{
    use iSchemaBuilderTrait;

    const OPTION_CHARACTER = 'character';
    const OPTION_COLLATE = 'collate';
    const OPTION_ENGINE = 'engine';
    const OPTION_COMMENT = 'comment';

    public function createTable($table, $columns, $options = [])
    {
        if (false === $this->tableExists($table)) {
            if ('imysql' === $this->db->getDriverName()) {
                if (is_array($options)) {
                    $tableOptions = [
                        sprintf('CHARACTER SET %s', I::value($options, 'character', 'utf8')),
                        sprintf('COLLATE %s', I::value($options, 'collate', 'utf8_unicode_ci')),
                        sprintf('ENGINE=%s', I::value($options, 'engine', 'InnoDB')),
                        sprintf('COMMENT = "%s"', I::value($options, 'comment', '')),
                    ];
                    $optionString = implode(' ', $tableOptions);
                } else {
                    $optionString = $options;
                }
            } else {
                $optionString = $options;
            }
            return parent::createTable($table, $columns, $optionString);
        }
    }

    public function tableExists($table)
    {
        if (preg_match("/{{%(.+)}}/", $table, $matches)) {
            $table = $matches[1];
        }
        return $this->db->createCommand()->tableExists($table);
    }
}
