<?php
namespace icy2003\php_tests\ihelpers;

use icy2003\php\ihelpers\Strings;

class StringsTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testByteLength()
    {
        $this->tester->assertEquals(7, Strings::byteLength('icy2003'));
        $this->tester->assertEquals(6, Strings::byteLength('你好'));
    }

    public function testLengh()
    {
        $this->tester->assertEquals(7, Strings::length('icy2003'));
        $this->tester->assertEquals(2, Strings::length('你好'));
    }

    public function testRandom()
    {
        $this->tester->assertIsString(Strings::random(10));
    }

    public function testToUnderline()
    {
        $this->tester->assertEquals('hello_world', Strings::toUnderline('helloWorld'));
    }

    public function testToCamel()
    {
        $this->tester->assertEquals('helloWorld', Strings::toCamel('hello_world'));
    }

    public function testToTitle()
    {
        $this->tester->assertEquals('Hello World', Strings::toTitle('hello world'));
    }

    public function testIsStartsWith()
    {
        $this->tester->assertTrue(Strings::isStartsWith('icy2003', 'i'));
    }

    public function testIsEndsWith()
    {
        $this->tester->assertTrue(Strings::isEndsWith('icy2003', '3'));
    }

    public function testIsContains()
    {
        $this->tester->assertTrue(Strings::isContains('icy2003', 'y2', $pos));
        $this->tester->assertEquals(2, $pos);
    }

    public function testPartBefore()
    {
        $this->tester->assertEquals('ic', Strings::partBefore('icy2003', 'y2', $pos));
        $this->tester->assertEquals(2, $pos);
    }

    public function testPartAfter()
    {
        $this->tester->assertEquals('003', Strings::partAfter('icy2003', 'y2', $pos));
        $this->tester->assertEquals(2, $pos);
    }

    public function testReverse()
    {
        $this->tester->assertEquals('abc', Strings::reverse('cba'));
        $this->tester->assertEquals('国中', Strings::reverse('中国'));
    }

    public function testSplit()
    {
        $this->tester->assertEquals(['a', 'b', 'c'], Strings::split('abc'));
        $this->tester->assertEquals(['中', '国'], Strings::split('中国'));
    }

    public function testToArray()
    {
        $this->tester->assertEquals(['a', 'b', 'c'], Strings::toArray('a,b,c'));
        $this->tester->assertEquals(['a', 'b', 'c'], Strings::toArray('a.b.c', '.'));
        $this->tester->assertEquals(['a', 'b', 'c'], Strings::toArray('a.b.c.c', '.', true));
        $this->tester->assertEquals(['a', 'b', 'c', 'c'], Strings::toArray(['a.b', 'c.c'], '.'));
        $this->tester->assertEquals(['a', 'b', 'c'], Strings::toArray(['a.b', 'c.c'], '.', true));
    }

    public function testSub()
    {
        $this->tester->assertEquals('icy', Strings::sub('icy2003', 0, 3));
        $this->tester->assertEquals('2003', Strings::sub('icy2003', 3));
    }

    public function testToNumber()
    {
        $this->tester->assertEquals(2, Strings::toNumber('2a2'));
        $this->tester->assertEquals(200, Strings::toNumber('2e2'));
        $this->tester->assertEquals(2.10, Strings::toNumber('2.1a'));
    }

}
