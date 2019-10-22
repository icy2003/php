<?php
/**
 * Class Html
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\iextensions\yii2\helpers;

use yii\base\InvalidArgumentException;
use yii\helpers\Html as H;

/**
 * Html 扩展
 */
class Html extends H
{
    /**
     * 获取 input 值
     *
     * @param yii\base\Model $model 模型对象
     * @param string $attribute
     * @param mixed $formName
     *
     * @return string
     */
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
            return $formName . $prefix . '[' . $attribute . ']' . $suffix;
        }

        throw new InvalidArgumentException(get_class($model) . '::formName() cannot be empty for tabular inputs.');
    }
}
