<?php
/**
 * Class Schema
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\iextensions\yii2\db\mysql;

use yii\db\mysql\Schema as S;

/**
 * Schema 扩展
 */
class Schema extends S
{
    /**
     * mediumtext 类型
     */
    const TYPE_MEDIUMTEXT = 'mediumtext';
    /**
     * longtext 类型
     */
    const TYPE_LONGTEXT = 'longtext';
}
