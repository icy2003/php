<?php

namespace icy2003\php_tests\ihelpers;

use icy2003\php\I;
use icy2003\php\ihelpers\Regular;

class RegularTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testEmail()
    {
        $this->tester->assertTrue(Regular::email('2317216477@qq.com'));
    }

    public function testIp()
    {
        $this->tester->assertTrue(Regular::ip('127.0.0.1'));
        $this->tester->assertFalse(Regular::ip('127.0.0.11111'));
    }

    public function testMobile()
    {
        $this->tester->assertTrue(Regular::mobile('13245678901'));
    }

    public function testIdCard()
    {
        $this->tester->assertTrue(Regular::idCard('123456199901010000'));
    }

    public function testUrl()
    {
        $this->tester->assertTrue(Regular::url('http://www.icy2003.com'));
        $this->tester->assertTrue(Regular::url('https://www.icy2003.com'));
    }

    public function testChinese()
    {
        $this->tester->assertTrue(Regular::chinese('中文'));
    }

    public function testJitOff()
    {
        Regular::jitOff(true);
        $this->tester->assertEquals(I::phpini('pcre.jit'), 1);
    }

    public function testIsLegal()
    {
        $this->tester->assertTrue(Regular::isLegal('/^https?:\/\//'));
        $this->tester->assertFalse(Regular::isLegal('^https?:\/\//'));
    }
}
