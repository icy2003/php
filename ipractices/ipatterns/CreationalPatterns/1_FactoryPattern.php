<?php

// @link http://www.runoob.com/design-pattern/factory-pattern.html

// 1. 创建一个接口
interface Shape
{
    public function draw();
}

// 2. 创建实现接口的实体类
class Rectangle implements Shape
{
    public function draw()
    {
        echo __METHOD__,PHP_EOL;
    }
}

class Square implements Shape
{
    public function draw()
    {
        echo __METHOD__,PHP_EOL;
    }
}
class Circle implements Shape
{
    public function draw()
    {
        echo __METHOD__,PHP_EOL;
    }
}

// 3. 创建一个工厂，生成基于给定信息的实体类的对象
class ShapeFactory
{
    public function getShape($shapeType)
    {
        if (null === $shapeType) {
            return null;
        }
        if ('rectangle' == strtolower($shapeType)) {
            return new Rectangle();
        }
        if ('square' == strtolower($shapeType)) {
            return new Square();
        }
        if ('circle' == strtolower($shapeType)) {
            return new Circle();
        }

        return null;
    }
}

// HTML 代码格式化
echo '<pre>',PHP_EOL;

// 设计模式的使用示例
$shapeFactory = new ShapeFactory();
$shap1 = $shapeFactory->getShape('rectangle');
$shap1->draw();
$shap2 = $shapeFactory->getShape('square');
$shap2->draw();
$shap3 = $shapeFactory->getShape('circle');
$shap3->draw();

// 显示图片
$file = dirname(__FILE__).'/'.basename(__FILE__, '.php').'.jpg';
include 'functions.php';
image($file);
