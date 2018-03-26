<?php

// @link http://www.runoob.com/design-pattern/abstract-factory-pattern.html

// 1. 为形状创建一个接口
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

// 3. 为颜色创建一个接口
interface Color
{
    public function fill();
}

// 4. 创建实现接口的实体类
class Red implements Color
{
    public function fill()
    {
        echo __METHOD__,PHP_EOL;
    }
}
class Green implements Color
{
    public function fill()
    {
        echo __METHOD__,PHP_EOL;
    }
}
class Blue implements Color
{
    public function fill()
    {
        echo __METHOD__,PHP_EOL;
    }
}

// 5. 为 Color 和 Shape 对象创建抽象类来获取工厂
 abstract class AbstractFactory
 {
     abstract public function getColor($color);

     abstract public function getShape($shape);
 }

// 6. 创建扩展了 AbstractFactory 的工厂类，基于给定的信息生成实体类的对象
class ShapeFactory extends AbstractFactory
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

    public function getColor($color)
    {
        return null;
    }
}

class ColorFactory extends AbstractFactory
{
    public function getShape($shape)
    {
        return null;
    }

    public function getColor($color)
    {
        if (null === $color) {
            return null;
        }
        if ('red' == strtolower($color)) {
            return new Red();
        }
        if ('green' == strtolower($color)) {
            return new Green();
        }
        if ('blue' == strtolower($color)) {
            return new Blue();
        }

        return null;
    }
}

// 7. 创建一个工厂创造器/生成器类，通过传递形状或颜色信息来获取工厂
class FactoryProducer
{
    public static function getFactory($choice)
    {
        if ('shape' == strtolower($choice)) {
            return new ShapeFactory();
        } elseif ('color' == strtolower($choice)) {
            return new ColorFactory();
        }

        return null;
    }
}

// HTML 代码格式化
echo '<pre>',PHP_EOL;

// 设计模式的使用示例
$shapeFactory = FactoryProducer::getFactory('SHAPE');

$shap1 = $shapeFactory->getShape('CIRCLE');
$shap1->draw();
$shap2 = $shapeFactory->getShape('RECTANGLE');
$shap2->draw();
$shap3 = $shapeFactory->getShape('SQUARE');
$shap3->draw();

$colorFactory = FactoryProducer::getFactory('COLOR');

$color1 = $colorFactory->getColor('RED');
$color1->fill();
$color2 = $colorFactory->getColor('GREEN');
$color2->fill();
$color3 = $colorFactory->getColor('BLUE');
$color3->fill();

// 显示图片
$file = dirname(__FILE__).'/'.basename(__FILE__, '.php').'.jpg';
include 'functions.php';
image($file);
