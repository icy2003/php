<?php

namespace icy2003\php_tests\icomponents\file;

use Exception;
use icy2003\php\I;
use icy2003\php\icomponents\file\LocalFile;

class LocalFileTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testBaseFunction()
    {
        $remoteFile = 'https://mirrors.aliyun.com/composer/composer.phar';
        $local = new LocalFile();
        $this->tester->assertTrue(($size1 = $local->getFilesize($remoteFile) > 0));
        $local = new LocalFile(['loader' => 'fopen']);
        $this->tester->assertTrue(($size2 = $local->getFilesize($remoteFile)) > 0);
        $local = new LocalFile(['loader' => 'fsockopen']);
        $this->tester->assertTrue(($size3 = $local->getFilesize($remoteFile)) > 0);
        $this->tester->assertEquals($size1, $size2);
        $this->tester->assertEquals($size2, $size3);
        try {
            $local->isFile('@icy2003/php/I.php1');
        } catch (Exception $e) {
            $this->tester->assertTrue(true);
        }
    }

    public function testSpl()
    {
        $local = new LocalFile();
        $spl = $local->spl('@icy2003/php_tests/_data/php.gif');
        $this->tester->assertTrue($spl instanceof \SplFileObject);
    }

    public function testSplInfo()
    {
        $local = new LocalFile();
        $splInfo = $local->splInfo('@icy2003/php_tests/_data/php.gif');
        $this->tester->assertTrue($splInfo instanceof \SplFileInfo);
    }

    public function testLinesGenerator()
    {
        $local = new LocalFile();
        foreach ($local->linesGenerator('@icy2003/php_tests/_data/data.txt', true) as $k => $line) {
            if ($k == 0) {
                $this->tester->assertEquals($line, '1');
            }
        }
    }

    public function testLine()
    {
        $local = new LocalFile();
        $this->tester->assertEquals($local->line('@icy2003/php_tests/_data/data.txt', 1), '22');
        $this->tester->assertTrue($local->line('@icy2003/php_tests/_data/data.txt', 111, true) === null);
    }

    public function testDataGenerator()
    {
        $local = new LocalFile();
        $buffer = '';
        $file = '@icy2003/php_tests/_data/data.txt';
        foreach ($local->dataGenerator($file, true) as $bf) {
            $buffer .= $bf;
        }
        $this->tester->assertEquals($buffer, file_get_contents(I::getAlias($file)));
    }
}
