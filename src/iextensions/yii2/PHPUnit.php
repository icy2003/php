<?php
/**
 * Class PHPUnit
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\iextensions\yii2;

/**
 * PHPUnit 扩展
 *
 * yii2 模型的单元测试断言快捷方式
 */
class PHPUnit extends \Codeception\Test\Unit
{
    /**
     * 断言 True
     *
     * @param yii\base\Model $model 模型对象
     * @param mixed $attributes
     *
     * @return void
     */
    public static function true($model, $attributes = null)
    {
        parent::assertTrue($model->validate($attributes), implode($model->getFirstErrors()));
    }

    /**
     * 断言 False
     *
     * @param yii\base\Model $model 模型对象
     * @param mixed $attributes
     *
     * @return void
     */
    public static function false($model, $attributes = null)
    {
        parent::assertFalse($model->validate($attributes), implode($model->getFirstErrors()));
    }

    /**
     * 断言属性
     *
     * @param yii\base\Model $model 模型对象
     * @param [type] $attribute 属性
     * @param [type] $array 格式：[错误的属性值,正确的属性值]，正确的属性可以不要
     *
     * @return void
     */
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
