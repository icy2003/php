<?php

namespace icy2003\php\iexts\yii2;

/**
 * yii2 模型的单元测试断言快捷方式
 */
class PHPUnit extends \Codeception\Test\Unit
{
    public static function true($model)
    {
        parent::assertTrue($model->validate(), implode($model->getFirstErrors()));
    }

    public static function false($model)
    {
        parent::assertFalse($model->validate(), implode($model->getFirstErrors()));
    }
}
