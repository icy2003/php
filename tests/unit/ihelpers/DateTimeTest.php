<?php

namespace icy2003\php_tests\ihelpers;

use icy2003\php\I;
use icy2003\php\ihelpers\DateTime;

class DateTimeTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testDayRange()
    {
        $datetime = new DateTime();
        $time = 1565627822;
        $this->tester->assertEquals($datetime->dayRange(0, $time), [1565625600, 1565711999]);
    }

    public function testWeekRange()
    {
        $datetime = new DateTime();
        $time = 1565627822;
        $this->tester->assertEquals($datetime->weekRange(0, $time), [1565452800, 1566057599]);
    }

    public function testMonthRange()
    {
        $datetime = new DateTime();
        $time = 1565627822;
        $this->tester->assertEquals($datetime->monthRange(0, $time), [1564588800, 1567267199]);
    }

    public function testYearRange()
    {
        $datetime = new DateTime();
        $time = 1565627822;
        $this->tester->assertEquals($datetime->yearRange(0, $time), [1546272000, 1577807999]);
    }

    public function testDay()
    {
        $datetime = new DateTime();
        $time = 1565627822;
        $this->tester->assertEquals($datetime->day(1, $time), 1565714222);
    }

    public function testWeek(){
        $datetime = new DateTime();
        $time = 1565627822;
        $this->tester->assertEquals($datetime->week(1, $time), 1566232622);
    }

    public function testMonth(){
        $datetime = new DateTime();
        $time = 1565627822;
        $this->tester->assertEquals($datetime->month(1, $time), 1568306222);
    }

    public function testYear(){
        $datetime = new DateTime();
        $time = 1565627822;
        $this->tester->assertEquals($datetime->year(1, $time), 1597250222);
    }

    public function testWeekName(){
        $datetime = new DateTime();
        $time = 1565627822;
        $this->tester->assertEquals($datetime->weekName($time), 'Tuesday');
    }

    public function testMonthName(){
        $datetime = new DateTime();
        $time = 1565627822;
        $this->tester->assertEquals($datetime->monthName($time), 'August');
    }

    public function testIsLeapYear(){
        $datetime = new DateTime();
        $time = 1565627822;
        $this->tester->assertFalse($datetime->isLeapYear($time));
    }

    public function testYearPosition(){
        $datetime = new DateTime();
        $time = 1565627822;
        $this->tester->assertEquals($datetime->yearPosition($time), 225);
    }
}
