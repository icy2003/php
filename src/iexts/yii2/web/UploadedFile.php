<?php

namespace icy2003\php\iexts\yii2\web;

use yii\web\UploadedFile as U;
use icy2003\php\iexts\yii2\helpers\Html;

class UploadedFile extends U
{
    public static function getInstance($model, $attribute, $formName = null)
    {
        $name = Html::getInputName($model, $attribute, $formName);
        return static::getInstanceByName($name);
    }
}
