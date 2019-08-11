<?php
namespace icy2003\php_tests\ihelpers;

use icy2003\php\ihelpers\Strings;
use Exception;

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
        $this->tester->assertEquals('', Strings::partBefore('icy2003', 'y3'));
    }

    public function testPartAfter()
    {
        $this->tester->assertEquals('003', Strings::partAfter('icy2003', 'y2', $pos));
        $this->tester->assertEquals(2, $pos);
        $this->tester->assertEquals('', Strings::partAfter('icy2003', 'y3', $pos));
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

    public function testMap()
    {
        $this->tester->assertEquals('1,2,3,4', Strings::map(function ($x) {
            return $x + 1;
        }, '0,1,2,3'));
    }

    public function testRepeat()
    {
        $this->tester->assertEquals('aaaa', Strings::repeat('a', 6, 4));
    }

    public function testPassword()
    {
        $password = Strings::generatePasswordHash('123456');
        $this->tester->assertTrue(Strings::validatePassword('123456', $password));
    }

    public function testToVariable()
    {
        $this->tester->assertEquals('{{param}}', Strings::toVariable('param'));
        $this->tester->assertEquals('||aa||', Strings::toVariable('aa', '||'));
    }

    public function testIsVariable()
    {
        $this->tester->assertTrue(Strings::isVariable('{{var}}'));
    }

    public function testFromVariable()
    {
        $this->tester->assertEquals('aaa', Strings::fromVariable('{{var}}', ['{{var}}' => 'aaa']));
    }

    public function testLooksLike()
    {
        $this->tester->assertTrue(Strings::looksLike('word', 'w0rd'));
        $this->tester->assertFalse(Strings::looksLike('word', 'word1'));
        $this->tester->assertFalse(Strings::looksLike('word', 'worl'));
    }

    public function testToPinyin()
    {
        $this->tester->assertEquals('zhongwen', Strings::toPinyin('中文'));
        $this->tester->assertEquals(['zhong', 'wen'], Strings::toPinyin('中文', true));
        $this->tester->assertEquals('zhong,wen', Strings::toPinyin('中文', ','));
        $this->tester->assertEquals('a', Strings::toPinyin('a'));
    }

    public function testToPinyinFirst()
    {
        $this->tester->assertEquals('zw', Strings::toPinyinFirst('中文'));
        $this->tester->assertEquals(['z', 'w'], Strings::toPinyinFirst('中文', true));
        $this->tester->assertEquals('z,w', Strings::toPinyinFirst('中文', ','));
    }

    public function testHide()
    {
        $this->tester->assertEquals('132******84', Strings::hide('13212345684', '******', '3?2'));
        $this->tester->assertEquals('132********', Strings::hide('13212345684', '********', '3?'));
        $this->tester->assertEquals('*********84', Strings::hide('13212345684', '*********', '?2'));
        try{
            $this->tester->assertEquals('*********84', Strings::hide('13212345684', '*********', '?2?'));
        }catch(Exception $e){
            $this->tester->assertTrue(true);
        }
        try{
            $this->tester->assertEquals('*********84', Strings::hide('13212345684', '*********', '?22'));
        }catch(Exception $e){
            $this->tester->assertTrue(true);
        }
        try{
            $this->tester->assertEquals('*********84', Strings::hide('13212345684', '*********', '?222'));
        }catch(Exception $e){
            $this->tester->assertTrue(true);
        }
        $this->tester->assertEquals('1', Strings::hide('1', '*********', '?2'));
    }

}
