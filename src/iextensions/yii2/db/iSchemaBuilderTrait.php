<?php
/**
 * Class iSchemaBuilderTrait
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\iextensions\yii2\db;

use icy2003\php\iextensions\yii2\db\mysql\Schema;

/**
 * iSchemaBuilderTrait 扩展
 */
trait iSchemaBuilderTrait
{

    /**
     * 返回 mediumtext
     *
     * @return yii\db\ColumnSchemaBuilder
     */
    public function mediumtext()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_MEDIUMTEXT);
    }

    /**
     * 返回 longtext
     *
     * @return yii\db\ColumnSchemaBuilder
     */
    public function longtext()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_LONGTEXT);
    }
}
