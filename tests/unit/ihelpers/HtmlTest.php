<?php

namespace icy2003\php_tests\ihelpers;

use icy2003\php\ihelpers\Html;

class HtmlTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testEncode(){
        $this->tester->assertEquals(Html::encode('<div>test</div>'), '&lt;div&gt;test&lt;/div&gt;');
    }

    public function testDecode(){
        $this->tester->assertEquals(Html::decode('&lt;div&gt;test&lt;/div&gt;'), '<div>test</div>');
    }

    public function testStripTags(){
        $this->tester->assertEquals(Html::stripTags('<div>test</div>'), 'test');
    }
}
