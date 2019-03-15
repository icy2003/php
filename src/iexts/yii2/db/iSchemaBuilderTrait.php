<?php

namespace icy2003\php\iexts\yii2\db;

use icy2003\php\iexts\yii2\db\mysql\Schema;

trait iSchemaBuilderTrait
{

    public function mediumtext()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_MEDIUMTEXT);
    }

    public function longtext()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_LONGTEXT);
    }
}
