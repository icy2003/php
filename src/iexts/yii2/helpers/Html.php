<?php

namespace icy2003\php\iexts\yii2\helpers;

use yii\base\InvalidArgumentException;
use yii\helpers\Html as H;

class Html extends H
{
    public static function getInputName($model, $attribute, $formName = null)
    {
        $formName = null === $formName ? $model->formName() : $formName;
        if (!preg_match(static::$attributeRegex, $attribute, $matches)) {
            throw new InvalidArgumentException('Attribute name must contain word characters only.');
        }
        $prefix = $matches[1];
        $attribute = $matches[2];
        $suffix = $matches[3];
        if ($formName === '' && $prefix === '') {
            return $attribute . $suffix;
        } elseif ($formName !== '') {
            return $formName . $prefix . "[$attribute]" . $suffix;
        }

        throw new InvalidArgumentException(get_class($model) . '::formName() cannot be empty for tabular inputs.');
    }
}
