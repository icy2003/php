<?php

namespace icy2003\php\iexts\yii2;

/**
 * yii2 模型的单元测试断言快捷方式
 */
class PHPUnit extends \Codeception\Test\Unit
{
    public static function true($model, $attributes = null)
    {
        parent::assertTrue($model->validate($attributes), implode($model->getFirstErrors()));
    }

    public static function false($model, $attributes = null)
    {
        parent::assertFalse($model->validate($attributes), implode($model->getFirstErrors()));
    }

    public static function checkAttribute($model, $attribute, $array)
    {
        if (2 >= count($array) && isset($array[0])) {
            $model->$attribute = $array[0];
            static::false($model, $attribute);
            if (isset($array[1])) {
                $model->$attribute = $array[1];
            }
        } else {
            throw new \Exception('数组 0 位元素表示错误的属性值，1 位元素表示正确的属性值');
        }
    }
}