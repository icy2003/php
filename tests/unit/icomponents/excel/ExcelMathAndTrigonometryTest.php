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

    public function testCosh()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::cosh(4), 27.308233));
    }

    public function testCot()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::cot(30), -0.156));
    }

    public function testCoth()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::coth(2), 1.037));
    }

    public function testCsc()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::csc(15), 1.538));
    }

    public function testCsch()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::csch(1.5), 0.4696));
    }

    public function testDecimal()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::decimal(111, 2), 7));
    }

    public function testDegrees()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::degrees(pi()), 180));
    }

    public function testEven()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::even(1.5), 2));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::even(3), 4));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::even(2), 2));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::even(-1), -2));
    }

    public function testExp()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::exp(1), 2.71828183));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::exp(2), 7.3890561));
    }

    public function testFact()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::fact(5), 120));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::fact(1.9), 1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::fact(0), 1));
        $this->tester->assertFalse(MathAndTrigonometry::fact(-1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::fact(1), 1));
    }

    public function testFactDouble()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::factdouble(6), 48));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::factdouble(7), 105));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::factdouble(0), 1));
    }

    public function testFloor()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::floor(3.7, 2), 2));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::floor(-2.5, -2), -2));
        $this->tester->assertFalse(MathAndTrigonometry::floor(2.5, -2));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::floor(1.58, 0.1), 1.5));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::floor(0.234, 0.01), 0.23));
    }

    public function testGcd()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::gcd(5, 2), 1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::gcd(24, 36), 12));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::gcd(7, 1), 1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::gcd(5, 0), 5));
    }

    public function testInt()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::int_i(8.9), 8));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::int_i(-8.9), -9));
    }

    public function testLcm()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::lcm(5, 2), 10));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::lcm(24, 36), 72));
    }

    public function testLn()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::ln(86), 4.4543473));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::ln(2.7182818), 1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::ln(MathAndTrigonometry::exp(3)), 3));
    }

    public function testLog()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::log(10), 1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::log(8, 2), 3));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::log(86, MathAndTrigonometry::exp(1)), 4.4543473));
    }

    public function testLog10()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::log10(86), 1.9345));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::log10(10), 1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::log10(100000), 5));
    }

    public function testMmult()
    {
        $this->tester->assertEquals(MathAndTrigonometry::mmult([[1, 3], [7, 2]], [[2, 0], [0, 2]]), [[2, 6], [14, 4]]);
        $this->tester->assertFalse(MathAndTrigonometry::mmult([[1, 3], [7, 2]], [[2, 0]]));
        $this->tester->assertEquals(MathAndTrigonometry::mmult([[14, 9, 3], [2, 11, 15]], [[200, 4], [250, 42], [425, 115]]), [[6325, 779], [9525, 2195]]);
    }

    public function testMod()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::mod(3, 2), 1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::mod(-3, 2), 1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::mod(3, -2), -1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::mod(-3, -2), -1));
    }

    public function testMround()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::mround(10, 3), 9));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::mround(-10, -3), -9));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::mround(1.3, 0.2), 1.4));
        $this->tester->assertFalse(MathAndTrigonometry::mround(5, -2));
    }

    public function testMultinomial()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::multinomial(2, 3, 4), 1260));
    }

    public function testMunit()
    {
        $this->tester->assertEquals(MathAndTrigonometry::munit(3), [[0 => 1], [1 => 1], [2 => 1]]);
    }

    public function testOdd()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::odd(1.5), 3));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::odd(3), 3));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::odd(2), 3));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::odd(-1), -1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::odd(-2), -3));
    }

    public function testPi()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::pi(), 3.141592654));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::pi() / 2, 1.570796327));
    }

    public function testPower()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::power(5, 2), 25));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::power(98.6, 3.2), 2401077.222));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::power(4, 5 / 4), 5.656854249));
    }

    public function testProduct()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::product([2, 3, 4]), 24));
    }

    public function testQuotient()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::quotient(5, 2), 2));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::quotient(4.5, 3.1), 1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::quotient(-10, 3), -3));
    }

    public function testRadians()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::radians(270), 4.712389));
    }

    public function testRand()
    {
        $this->tester->assertTrue(MathAndTrigonometry::rand() < 1);
        $this->tester->assertTrue(MathAndTrigonometry::rand() >= 0);
    }

    public function testRandArray()
    {
        $array = MathAndTrigonometry::randarray(5, 2, 3, 10);
        foreach ($array as $row) {
            foreach ($row as $value) {
                $this->tester->assertTrue($value < 10);
                $this->tester->assertTrue($value >= 3);
            }
        }
    }

    public function testRandBetween()
    {
        $this->tester->assertTrue(MathAndTrigonometry::randbetween(5, 19) < 19);
        $this->tester->assertTrue(MathAndTrigonometry::randbetween(5, 19) >= 5);
    }

    public function testRoman()
    {
        $this->tester->assertEquals(MathAndTrigonometry::roman(499), 'CDXCIX');
        $this->tester->assertFalse(MathAndTrigonometry::roman(4999));
    }

    public function testRound()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::round(2.15, 1), 2.2));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::round(2.149, 1), 2.1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::round(-1.475, 2), -1.48));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::round(21.5, -1), 20));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::round(626.3, -3), 1000));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::round(1.98, -1), 0));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::round(-50.55, -2), -100));
    }

    public function testRoundDown()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::rounddown(3.2, 0), 3));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::rounddown(76.9, 0), 76));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::rounddown(3.14159, 3), 3.141));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::rounddown(-3.14159, 1), -3.1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::rounddown(31415.92654, -2), 31400));
    }

    public function testRoundUp()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::roundup(3.2, 0), 4));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::roundup(76.9, 0), 77));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::roundup(3.14159, 3), 3.142));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::roundup(-3.14159, 1), -3.2));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::roundup(31415.92654, -2), 31500));
    }

    public function testSec()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sec(45), 1.90359));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sec(30), 6.48292));
    }

    public function testSech()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sech(45), 5.73e-20));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sech(30), 1.87e-13));
    }

    public function testSeriessum()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::seriessum(3, 2, 1, [1, 2, 3, 4]), 1278));
    }

    public function testSequence()
    {
        $this->tester->assertEquals(MathAndTrigonometry::sequence(4, 5), [[1, 2, 3, 4, 5], [6, 7, 8, 9, 10], [11, 12, 13, 14, 15], [16, 17, 18, 19, 20]]);
    }

    public function testSign()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sign(10), 1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sign(4 - 4), 0));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sign(-0.00001), -1));
    }

    public function testSin()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sin(pi()), 0));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sin(pi() / 2), 1));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sin(pi() * 30 / 180), 0.5));
    }

    public function testSinh()
    {
        $this->tester->assertTrue(Numbers::isEquals(2.868 * MathAndTrigonometry::sinh(0.0342 * 1.03), 0.1010491));
    }

    public function testSqrt()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sqrt(16), 4));
    }

    public function testSqrtpi()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sqrtpi(1), 1.772454));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sqrtpi(2), 2.506628));
    }

    public function testSum()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sum(4, 7, 9, 7, 8), 35));
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sum([4, 7, 9, 7, 8]), 35));
    }

    public function testSumProduct()
    {
        $this->tester->assertTrue(Numbers::isEquals(MathAndTrigonometry::sumproduct([[3.25, 2.2, 4.2, 0.08], [2, 1, 2, 6]]), 17.58));
    }
}
