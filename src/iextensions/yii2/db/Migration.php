<?php

/**
 * Class Migration
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\iextensions\yii2\db;

use icy2003\php\I;
use yii\db\Migration as DbMigration;

/**
 * Migration 扩展
 */
class Migration extends DbMigration
{
    use iSchemaBuilderTrait;

    /**
     * character 设置
     */
    const OPTION_CHARACTER = 'character';
    /**
     * collate 设置
     */
    const OPTION_COLLATE = 'collate';

    /**
     * engine 设置
     */
    const OPTION_ENGINE = 'engine';

    /**
     * comment 设置
     */
    const OPTION_COMMENT = 'comment';

    /**
     * 创建一个表
     *
     * @param string $table
     * @param array $columns
     * @param array $options
     *
     * @return void
     */
    public function createTable($table, $columns, $options = [])
    {
        if (false === $this->tableExists($table)) {
            if ('imysql' === $this->db->getDriverName()) {
                if (is_array($options)) {
                    $tableOptions = [
                        sprintf('CHARACTER SET %s', I::get($options, 'character', 'utf8mb4')),
                        sprintf('COLLATE %s', I::get($options, 'collate', 'utf8mb4_unicode_ci')),
                        sprintf('ENGINE=%s', I::get($options, 'engine', 'InnoDB')),
                        sprintf('COMMENT = "%s"', I::get($options, 'comment', '')),
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

    /**
     * 判断表是否存在
     *
     * 支持 yii2 的 {{}} 格式
     *
     * @param string $table
     *
     * @return boolean
     */
    public function tableExists($table)
    {
        if (preg_match('/{{%(.+)}}/', $table, $matches)) {
            $table = $matches[1];
        }
        return $this->db->createCommand()->tableExists($table);
    }

    /**
     * 删除一个表
     *
     * 在 migration 中如果表已经不在了，那也认为删除成功
     *
     * @param string $table
     *
     * @return boolean
     */
    public function dropTable($table)
    {
        if (true === $this->tableExists($table)) {
            return parent::dropTable($table);
        }
        return true;
    }
}
