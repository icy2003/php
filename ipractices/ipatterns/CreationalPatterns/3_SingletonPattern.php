<?php

// @link http://www.runoob.com/design-pattern/singleton-pattern.html

// 1. 创建一个 Singleton 类
class SingleObject
{
    private static $instance;

    // 防止实例化对象
    private function __construct()
    {
    }

    // 防止克隆对象
    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function showMessage()
    {
        echo __METHOD__,PHP_EOL;
    }
}

echo '<pre>',PHP_EOL;

SingleObject::getInstance()->showMessage();

$file = dirname(__FILE__).'/'.basename(__FILE__, '.php').'.jpg';

include 'functions.php';

image($file);

