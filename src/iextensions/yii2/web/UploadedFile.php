<?php
/**
 * Class UploadedFile
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\iextensions\yii2\web;

use yii\web\UploadedFile as U;
use icy2003\php\iextensions\yii2\helpers\Html;

/**
 * UploadedFile 扩展
 */
class UploadedFile extends U
{
    /**
     * 获取单例
     *
     * @param yii\base\Model $model 模型对象
     * @param string $attribute
     * @param mixed $formName
     *
     * @return \yii\web\UploadedFile
     */
    public static function getInstance($model, $attribute, $formName = null)
    {
        $name = Html::getInputName($model, $attribute, $formName);
        return static::getInstanceByName($name);
    }
}
