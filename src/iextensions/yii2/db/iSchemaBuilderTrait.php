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
     * @return \yii\db\ColumnSchemaBuilder
     */
    public function mediumtext()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_MEDIUMTEXT);
    }

    /**
     * 返回 longtext
     *
     * @return \yii\db\ColumnSchemaBuilder
     */
    public function longtext()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_LONGTEXT);
    }

    /**
     * ID 快捷方式
     * 
     * @param integer $length ID 长度限制，默认 11
     * @param mixed $defaultValue 默认值为 0
     *
     * @return \yii\db\ColumnSchemaBuilder
     */
    public function id($length = 11, $defaultValue = 0)
    {
        return $this->integer($length)->unsigned()->notNull()->defaultValue($defaultValue);
    }

    /**
     * 普通字符串快捷方式
     * 
     * @param integer $length 字符串长度限制
     * @param string $defaultValue 默认字符串值，默认为 ''
     *
     * @return \yii\db\ColumnSchemaBuilder
     */
    public function content($length = null, $defaultValue = '')
    {
        return $this->string($length)->notNull()->defaultValue($defaultValue);
    }

    /**
     * 时间戳快捷方式
     * 
     * @param string $defaultValue 默认时间戳，默认为 null，值可以是数字或者字符串，自动转为时间戳，null 时为 0
     *
     * @return \yii\db\ColumnSchemaBuilder
     */
    public function timestamp($defaultValue = null)
    {
        if (null === $defaultValue) {
            $defaultValue = 0;
        } elseif (false !== ($time = strtotime($defaultValue))) {
            $defaultValue = $time;
        }
        return $this->id(11, $defaultValue);
    }
}
