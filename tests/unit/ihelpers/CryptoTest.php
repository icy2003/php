<?php

namespace icy2003\php_tests\ihelpers;

use Exception;
use icy2003\php\I;
use icy2003\php\ihelpers\Crypto;

class CryptoTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * crypto
     *
     * @var Crypto
     */
    protected $_crypto;

    public function _before()
    {
        I::ini('EXT_LOADED', false);
        try {
            $this->_crypto = new Crypto();
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
        I::ini('EXT_LOADED', true);
        try {
            $this->_crypto = new Crypto();
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testSetDigestAlg()
    {
        try {
            $this->_crypto->setDigestAlg('xxx');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
        $this->_crypto->setDigestAlg('SHA256');
    }

    public function testSetPrivateKeyBits()
    {
        $this->_crypto->setPrivateKeyBits(2048);
    }

    public function testSetPrivateKeyType()
    {
        try {
            $this->_crypto->setPrivateKeyType(11);
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
        $this->_crypto->setPrivateKeyType(OPENSSL_KEYTYPE_RSA);
    }

    public function testSetConfig()
    {
        try {
            $this->_crypto->setConfig('xxx');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
        $this->_crypto->setConfig('@icy2003/php/openssl.cnf');
    }

    public function testSetPassword()
    {
        $this->_crypto->setPassword(123456);
    }

}
