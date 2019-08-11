<?php

namespace icy2003\php_tests;

use Exception;
use icy2003\php\C;

class CTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testAssertExtension()
    {
        $loaded = extension_loaded('mb_string');
        if (false === $loaded) {
            try {
                C::assertExtension('mb_string', 'xx');
            } catch (Exception $e) {
                $this->tester->assertTrue(true);
            }
        } else {
            C::assertExtension('mb_string', 'xx');
            $this->tester->assertTrue(true);
        }
    }

    public function testAssertFunction()
    {
        try {
            C::assertFunction('in_array1', 'xx');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
        C::assertFunction('in_array', 'xx');
        $this->tester->assertTrue(true);
    }

    public function testAssertTrue()
    {
        C::assertTrue(true, 'xxx');
        $this->tester->assertTrue(true);
        try {
            C::assertTrue(0, 'xxx');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
        try {
            C::assertTrue(1, 'xxx');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
        try {
            C::assertTrue(false, 'xxx');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testAssertNotTrue()
    {
        C::assertNotTrue(0, 'xxx');
        $this->tester->assertTrue(true);
        C::assertNotTrue(1, 'xxx');
        $this->tester->assertTrue(true);
        C::assertNotTrue(false, 'xxx');
        $this->tester->assertTrue(true);
        try {
            C::assertNotTrue(true, 'xxx');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testAssertFalse()
    {
        C::assertFalse(false, 'xxx');
        $this->tester->assertTrue(true);
        try {
            C::assertFalse(0, 'xxx');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
        try {
            C::assertFalse(1, 'xxx');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
        try {
            C::assertFalse(true, 'xxx');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testAssertNotFalse()
    {
        C::assertNotFalse(0, 'xxx');
        $this->tester->assertTrue(true);
        C::assertNotFalse(1, 'xxx');
        $this->tester->assertTrue(true);
        C::assertNotFalse(true, 'xxx');
        $this->tester->assertTrue(true);
        try {
            C::assertNotFalse(false, 'xxx');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

}
