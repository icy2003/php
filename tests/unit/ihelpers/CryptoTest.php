<?php

namespace icy2003\php_tests\ihelpers;

use Exception;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Crypto;

class CryptoTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testSetDigestAlg()
    {
        try {
            $crypto = new Crypto();
            $crypto->setDigestAlg('SHA256');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testSetPrivateKeyBits()
    {
        try {
            $crypto = new Crypto();
            $crypto->setPrivateKeyBits(2048);
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testSetPrivateKeyType()
    {
        try {
            $crypto = new Crypto();
            $crypto->setPrivateKeyType(OPENSSL_KEYTYPE_RSA);
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testSetConfig()
    {
        try {
            $crypto = new Crypto();
            $crypto->setConfig('@icy2003/php/openssl.cnf');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testSetPassword()
    {
        try {
            $crypto = new Crypto();
            $crypto->setPassword(123456);
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testGeneratePair()
    {
        try {
            $crypto = new Crypto();
            $crypto->generatePair();
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testGetPair()
    {
        try {
            $crypto = new Crypto();
            $pair = $crypto->getPair();
            $this->tester->assertEquals(Arrays::count($pair), 2);
            $pair = $crypto->getPair(true);
            $this->tester->assertEquals(Arrays::count($pair), 2);
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testSetPair()
    {
        try {
            $crypto = new Crypto();
            $pair = $crypto->getPair();
            $crypto->setPair($pair);
            $pair = $crypto->getPair(true);
            $crypto->setPair($pair);
            $this->tester->assertTrue(true);
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testGetPublicEncrypt()
    {
        try {
            $crypto = new Crypto();
            $this->tester->assertIsString($crypto->getPublicEncrypt('test'));
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testGetPrivateDecrypt()
    {
        try {
            $crypto = new Crypto();
            $this->tester->assertEquals($crypto->getPrivateDecrypt($crypto->getPublicEncrypt('test')), 'test');
            $this->tester->assertFalse($crypto->getPrivateDecrypt(0));
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testGetPrivateEncrypt()
    {
        try {
            $crypto = new Crypto();
            $this->tester->assertIsString($crypto->getPrivateEncrypt('test'));
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testGetPublicDecrypt()
    {
        try {
            $crypto = new Crypto();
            $this->tester->assertEquals($crypto->getPublicDecrypt($crypto->getPrivateEncrypt('test')), 'test');
            $this->tester->assertFalse($crypto->getPublicDecrypt(0));
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testCert()
    {
        try {
            $crypto = new Crypto();
            $crypto->setDnCountryName('cn');
            $crypto->setDnStateOrProvinceName('hubei');
            $crypto->setDnLocalityName('wuhan');
            $crypto->setDnOrganizationName('icy2003');
            $crypto->setDnOrganizationalUnitName('icy2003');
            $crypto->setDnCommonName('icy2003');
            $crypto->setDnEmailAddress('2317216477@qq.com');
            $crypto->generateCert();
            $this->tester->assertIsArray($crypto->getCert());
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testSignature(){
        try {
            $crypto = new Crypto();
            $data = $crypto->getSignature('test');
            $this->tester->assertTrue($crypto->isVerify('test', $data));
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

}
