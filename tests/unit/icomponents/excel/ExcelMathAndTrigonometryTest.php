<?php

namespace icy2003\php_tests\icomponents\excel;

use icy2003\php\icomponents\excel\MathAndTrigonometry;
use icy2003\php\ihelpers\Numbers;

class MathAndTrigonometryTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testAbs()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::abs(2), 2));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::abs(-2), 2));
    }

    public function testAcos()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::acos(-0.5), 2.094395102));
    }

    public function testAcosh()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::acosh(1), 0));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::acosh(10), 2.9932228));
    }

    public function testAcot()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::acot(2), 0.4636));
    }

    public function testAcoth()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::acoth(6), 0.168));
    }

    public function testArabic()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::arabic('LVII'), 57));
    }

    public function testAsin()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::asin(-0.5), -0.523598776));
    }

    public function testAsinh()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::asinh(-2.5), -1.647231146));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::asinh(10), 2.99822295));
    }

    public function testAtan()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::atan(1), pi() / 4));
    }

    public function testAtan2()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::atan2(1, 1), pi() / 4));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::atan2(-1, -1), -2.35619449));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::atan2(0, -1), -pi() / 2));
    }

    public function testAtanh()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::atanh(0.76159416), 1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::atanh(-0.1), -0.100335348));
    }

    public function testBase()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::base(7, 2), 111));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::base(100, 16), 64));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::base(15, 2, 10), '0000001111'));
    }

    public function testCeiling()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::ceiling(2.5, 1), 3));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::ceiling(-2.5, -2), -4));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::ceiling(-2.5, 2), -2));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::ceiling(1.5, 0.1), 1.5));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::ceiling(0.234, 0.01), 0.24));
    }

    public function testCombin()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::combin(8, 2), 28));
    }

    public function testCombina()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::combina(4, 3), 20));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::combina(10, 3), 220));
    }

    public function testCos()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::cos(1.047), 0.5001711));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::cos(60 * pi() / 180), 0.5));
    }
}
