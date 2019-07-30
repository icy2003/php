<?php

namespace icy2003\php_tests\ihelpers;

use icy2003\php\ihelpers\Color;

class ColorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testToRGB()
    {
        $color = new Color('red');
        $this->tester->assertEquals($color->toRGB()->get(), [255, 0, 0]);
    }

    public function testToHex()
    {
        $color = new Color([255, 0, 0]);
        $this->tester->assertEquals($color->toHex()->get(), 'FF0000');
    }

    public function testToCMYK()
    {
        $color = new Color('#FFAA00');
        $this->tester->assertEquals($color->toCMYK()->get(), [0.0000, 0.3333, 1.0000, 0.0000]);
    }
}
