<?php

// @link http://www.runoob.com/design-pattern/builder-pattern.html

// 1. 创建一个表示食物条目和食物包装的接口
interface Item
{
    public function name();

    public function packing();

    public function price();
}

interface Packing
{
    public function pack();
}

// 2. 创建实现 Packing 接口的实体类
class Wrapper implements Packing
{
    public function pack()
    {
        echo 'Wrapper';
    }
}

class Bottle implements Packing
{
    public function pack()
    {
        echo 'Bottle';
    }
}

// 3. 创建实现 Item 接口的抽象类，该类提供了默认的功能
abstract class Burger implements Item
{
    public function packing()
    {
        return new Wrapper();
    }

    abstract public function price();
}

abstract class ColdDrink implements Item
{
    public function packing()
    {
        return new Bottle();
    }

    abstract public function price();
}

// 4. 创建扩展了 Burger 和 ColdDrink 的实体类
class VegBurger extends Burger
{
    public function price()
    {
        return 25.0;
    }

    public function name()
    {
        return  'Veg Burger';
    }
}

class ChickenBurger extends Burger
{
    public function price()
    {
        return 50.5;
    }

    public function name()
    {
        return  'Chicken Burger';
    }
}

class Coke extends ColdDrink
{
    public function price()
    {
        return 30.0;
    }

    public function name()
    {
        return 'Coke';
    }
}

class Pepsi extends ColdDrink
{
    public function price()
    {
        return 35.0;
    }

    public function name()
    {
        return 'Pepsi';
    }
}

// 5. 创建一个 Meal 类，带有上面定义的 Item 对象
class Meal
{
    private $_items = [];

    public function addItem($item)
    {
        $this->_items[] = $item;
    }

    public function getCost()
    {
        $cost = 0.0;
        foreach ($this->_items as $item) {
            $cost += $item->price();
        }

        return $cost;
    }

    public function showItems()
    {
        foreach ($this->_items as $item) {
            echo 'Item:',$item->name();
            echo ' Packing:',$item->packing()->pack();
            echo ' Price:',$item->price(),PHP_EOL;
        }
    }
}

// 6. 创建一个 MealBuilder 类，实际的 builder 类负责创建 Meal 对象
class MealBuilder
{
    public function prepareVegMeal()
    {
        $meal = new Meal();
        $meal->addItem(new VegBurger());
        $meal->addItem(new Coke());

        return $meal;
    }

    public function prepareNonVegMeal()
    {
        $meal = new Meal();
        $meal->addItem(new ChickenBurger());
        $meal->addItem(new Pepsi());

        return $meal;
    }
}

// HTML 代码格式化
echo '<pre>',PHP_EOL;

// 设计模式的使用示例
$mealBuilder = new MealBuilder();

$vegMeal = $mealBuilder->prepareVegMeal();
echo 'Veg Meal',PHP_EOL;
$vegMeal->showItems();
echo 'Total Cost:',$vegMeal->getCost(),PHP_EOL;

$nonVegMeal = $mealBuilder->prepareNonVegMeal();
echo 'Non-Veg Meal';
$nonVegMeal->showItems();
echo 'Total Cost:',$nonVegMeal->getCost(),PHP_EOL;

// 显示图片
$file = dirname(__FILE__).'/'.basename(__FILE__, '.php').'.jpg';
include 'functions.php';
image($file);


