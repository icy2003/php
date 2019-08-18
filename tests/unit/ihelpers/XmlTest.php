<?php

namespace icy2003\php_tests\ihelpers;

use icy2003\php\ihelpers\Json;
use icy2003\php\ihelpers\Xml;

class XmlTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $_string = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<object>
    <name>icy2003</name>
</object>
EOT;

    public function testIsXml()
    {
        $this->tester->assertTrue(Xml::isXml($this->_string));
        $this->tester->assertFalse(Xml::isXml($this->_string . 'x'));
    }

    public function testToArray()
    {
        $this->tester->assertEquals(Xml::toArray($this->_string), [
            'name' => 'icy2003',
        ]);
        $this->tester->assertEquals(Xml::toArray($this->_string . 'x'), []);
    }

    public function testFromArray()
    {
        $this->tester->assertEquals(Xml::fromArray([
            'name' => 'icy2003',
            'number' => 2003,
            'info' => [
                'web' => 'https://www.icy2003.com',
            ],
        ]), '<xml><name><![CDATA[icy2003]]></name><number>2003</number><info><web><![CDATA[https://www.icy2003.com]]></web></info></xml>');
    }
}
