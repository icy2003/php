<?php

// @link http://www.runoob.com/design-pattern/prototype-pattern.html

// 1. 创建一个 Shape 抽象类
abstract class Shape
{
    private $id;
    protected $type;

    abstract public function draw();

    public function getType()
    {
        return $this->type;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}

// 2. 创建扩展了上面抽象类的实体类
class Rectangle extends Shape
{
    public function __construct()
    {
        $this->type = 'Rectangle';
    }

    public function draw()
    {
        echo __METHOD__;
    }
}

class Square extends Shape
{
    public function __construct()
    {
        $this->type = 'Square';
    }

    public function draw()
    {
        echo __METHOD__;
    }
}

class Circle extends Shape
{
    public function __construct()
    {
        $this->type = 'Circle';
    }

    public function draw()
    {
        echo __METHOD__;
    }
}

// 3. 创建一个类，从数据库获取实体类，并把它们存储在一个 Hashtable 中（PHP 里其实就是数组）
class ShapeCache
{
    private static $shapeMap = [];

    public static function getShape($shapeId)
    {
        return clone static::$shapeMap[$shapeId];
    }

    public static function loadCache()
    {
        $circle = new Circle();
        $circle->setId(1);
        static::$shapeMap[$circle->getId()] = $circle;

        $square = new Square();
        $square->setId(2);
        static::$shapeMap[$square->getId()] = $square;

        $rectangle = new Rectangle();
        $rectangle->setId(3);
        static::$shapeMap[$rectangle->getId()] = $rectangle;
    }
}

// HTML 代码格式化
echo '<pre>',PHP_EOL;

// 设计模式的使用示例
ShapeCache::loadCache();

$cloneShape = ShapeCache::getShape(1);
echo $cloneShape->getType(), ',', $cloneShape->draw(), PHP_EOL;

$cloneShape = ShapeCache::getShape(2);
echo $cloneShape->getType(), ',', $cloneShape->draw(), PHP_EOL;

$cloneShape = ShapeCache::getShape(3);
echo $cloneShape->getType(), ',', $cloneShape->draw(), PHP_EOL;

// 显示图片
$file = dirname(__FILE__).'/'.basename(__FILE__, '.php').'.jpg';
include 'functions.php';
image($file);
