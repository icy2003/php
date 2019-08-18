<?php

namespace icy2003\php_tests;

use Exception;
use icy2003\php\I;

class A
{
    public function getAa()
    {
        return '111';
    }

    public function setC($v)
    {
        $this->bb = $v;
    }

    public $bb = [
        'c' => ['d' => 'efg'],
    ];
}

class ITest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $_i;

    public function testGet()
    {
        $this->tester->assertEquals(I::get(true, 'a', 'b'), 'a');
        $this->tester->assertEquals(I::get(false, 'a', 'b'), 'b');
        $this->tester->assertEquals(I::get(['a' => 'aa'], 'a'), 'aa');
        $this->tester->assertEquals(I::get(['a' => 'aa'], 'b', 'cc'), 'cc');
        $this->tester->assertEquals(I::get(['a' => ['b' => 'c']], 'a.b'), 'c');
        $a = new A();
        $this->tester->assertEquals(I::get($a, 'aa'), '111');
        $this->tester->assertEquals(I::get($a, 'bb.c.d'), 'efg');
        $this->tester->assertEquals(I::get($a, 'bb.c.d.1'), 'f');
        $this->tester->assertEquals(I::get(null, 'i', 'xx'), 'xx');
        $this->tester->assertEquals(I::get(function () {
            return true;
        }, function () {
            $this->_i = 1;
        }, function () {
            $this->_i = 2;
        }), true);
        $this->tester->assertEquals($this->_i, 1);
        $this->tester->assertEquals(I::get(function () {
            return false;
        }, function () {
            $this->_i = 1;
        }, function () {
            $this->_i = 2;
        }), false);
        $this->tester->assertEquals($this->_i, 2);
        $fp = fopen(__FILE__, 'rb');
        $this->tester->assertEquals(I::get($fp, '', 'aa'), 'aa');
    }

    public function testSet()
    {
        $array = ['a' => 1];
        I::set($array, 'b', 2);
        $this->tester->assertEquals($array, ['a' => 1, 'b' => 2]);
        $a = new A();
        I::set($a, 'c', 'xx');
        $this->tester->assertEquals(I::get($a, 'bb'), 'xx');
        I::set($a, 'bb', 'yy');
        $this->tester->assertEquals(I::get($a, 'bb'), 'yy');
        try {
            I::set($a, 'bb1', 'yy');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
        $array = ['a' => 1];
        I::set($array, 'a', 2, false);
        $this->tester->assertEquals($array, ['a' => 1]);
    }

    public function testCall()
    {
        $this->tester->assertEquals(I::call('trim', ['aaa    ']), 'aaa');
    }

    public function testDef()
    {
        I::def('AAA', 1);
        $this->tester->assertEquals(AAA, 1);
    }

    public function testIsEmpty()
    {
        $this->tester->assertTrue(I::isEmpty(''));
    }

    public function testPhpini()
    {
        ini_set('set_time_limit', 0);
        $this->tester->assertEquals(I::phpini('max_execution_time'), 0);
    }

    public function testDisplayErrors(){
        I::displayErrors();
        $this->tester->assertTrue(true);
    }

    public function testSetAlias(){
        I::setAlias('@_data', '@icy2003/php_tests/_data');
        $this->tester->assertEquals(I::getAlias('@_data'), I::getAlias('@icy2003/php_tests/_data'));
    }

}
