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
        try {
            $color = new Color('GGG000');
        } catch (\Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testToHex()
    {
        $color = new Color([255, 0, 0]);
        $this->tester->assertEquals($color->toHex()->get(), 'FF0000');
        $color = new Color([255, 0, 0], Color::TYPE_RGB);
        $this->tester->assertEquals($color->toHex()->get(), 'FF0000');
        $color = new Color([0.0000, 0.3333, 1.0000, 0.0000]);
        $this->tester->assertEquals($color->toHex()->get(), 'FFAA00');
    }

    public function testToCMYK()
    {
        $color = new Color('#AABBCC');
        $this->tester->assertEquals($color->toCMYK()->get(), [0.1667, 0.0833, 0.0000, 0.2000]);
        $color = new Color('#000000');
        $this->tester->assertEquals($color->toCMYK()->get(), [0.0000, 0.0000, 0.0000, 1.0000]);
        $color = new Color('#FFFFFF');
        $this->tester->assertEquals($color->toCMYK()->get(), [0.0000, 0.0000, 0.0000, 0.0000]);
    }

    public function testToString(){
        $color = new Color('red');
        ob_start();
        echo $color->toRGB();
        $this->tester->assertEquals(ob_get_contents(),'255,0,0');
        ob_end_clean();
    }
}
