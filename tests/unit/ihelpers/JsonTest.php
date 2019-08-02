<?php

namespace icy2003\php_tests\ihelpers;

use icy2003\php\ihelpers\Json;

class JsonTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testEncode()
    {
        $this->tester->assertEquals(Json::encode(['a' => 1]), '{"a":1}');
    }

    public function testDecode()
    {
        $this->tester->assertEquals(Json::decode('{"a":1}'), ['a' => 1]);
    }

    public function testIsJson()
    {
        $this->tester->assertFalse(Json::isJson(1));
        $this->tester->assertFalse(Json::isJson("aaa"));
        $this->tester->assertTrue(Json::isJson('{"a":1}'));
    }

    public function testGet(){
        $this->tester->assertEquals(Json::get('{"a":1}', "a"), '1');
    }
}
