<?php

namespace icy2003\php\iexts\yii2\db;

use icy2003\php\ihelpers\Env;
use yii\db\Migration as M;

class Migration extends M
{
    use iSchemaBuilderTrait;

    const OPTION_CHARACTER = 'character';
    const OPTION_COLLATE = 'collate';
    const OPTION_ENGINE = 'engine';
    const OPTION_COMMENT = 'comment';

    public function createTable($table, $columns, $options = null)
    {
        if ('imysql' === $this->db->getDriverName()) {
            null === $options && $options = [];
            if (is_array($options)) {
                $tableOptions = [
                    sprintf('CHARACTER SET %s', Env::value($options, 'character', 'utf8')),
                    sprintf('COLLATE %s', Env::value($options, 'collate', 'utf8_unicode_ci')),
                    sprintf('ENGINE=%s', Env::value($options, 'engine', 'InnoDB')),
                    sprintf('COMMENT = "%s"', Env::value($options, 'comment', '')),
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
