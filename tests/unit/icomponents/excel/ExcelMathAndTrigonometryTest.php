<?php

namespace icy2003\php_tests\icomponents\excel;

use icy2003\php\icomponents\excel\MathAndTrigonometry;

class MathAndTrigonometryTest extends \Codeception\Test\Unit
{
    public function testAbs()
    {
        parent::assertEquals(MathAndTrigonometry::abs(2), 2);
        parent::assertEquals(MathAndTrigonometry::abs(-2), 2);
    }

    public function testAcos()
    {
        parent::assertTrue(abs(MathAndTrigonometry::acos(-0.5) - 2.094395102) < 0.01);
    }

    public function testAcosh()
    {
        parent::assertTrue(abs(MathAndTrigonometry::acosh(1) - 0) < 0.01);
        parent::assertTrue(abs(MathAndTrigonometry::acosh(10) - 2.9932228) < 0.01);
    }

    public function testAcot()
    {
        parent::assertTrue(abs(MathAndTrigonometry::acot(2) - 0.4636) < 0.01);
    }

    public function testAcoth()
    {
        parent::assertTrue(abs(MathAndTrigonometry::acoth(6) - 0.168) < 0.01);
    }

    public function testArabic()
    {
        parent::assertEquals(MathAndTrigonometry::arabic('LVII'), 57);
    }

    public function testAsin()
    {
        parent::assertTrue(abs(MathAndTrigonometry::asin(-0.5) - (-0.523598776)) < 0.01);
    }

    public function testAsinh()
    {
        parent::assertTrue(abs(MathAndTrigonometry::asinh(-2.5) - (-1.647231146)) < 0.01);
        parent::assertTrue(abs(MathAndTrigonometry::asinh(10) - 2.99822295) < 0.01);
    }
}
