<?php

namespace icy2003\php_tests\ihelpers;

use icy2003\php\I;
use icy2003\php\ihelpers\Charset;

class CharsetTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _getTxt($charset = 'GB2312')
    {
        return file_get_contents(I::getAlias('@icy2003/php_tests/_data/charsets/' . $charset . '.txt'));
    }

    public function testDetect()
    {
        $this->tester->assertEquals(Charset::detect('中文'), 'UTF-8');
    }

    public function testToUtf()
    {
        $this->tester->assertEquals(Charset::toUtf('a'), 'a');
    }

    public function testToCn()
    {
        $this->tester->assertEquals(Charset::toCn('中'), $this->_getTxt());
    }

    public function testIsUtf8()
    {
        $this->tester->assertTrue(Charset::isUtf8('中'));
    }

    public function testConvertTo()
    {
        $this->tester->assertEquals(Charset::convertTo('中', 'EUC-CN'), $this->_getTxt());
        $this->tester->assertEquals(Charset::convertTo('é', 'ASCII'), '\'e');
    }
}
