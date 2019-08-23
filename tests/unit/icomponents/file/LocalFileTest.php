<?php

namespace icy2003\php_tests\icomponents\file;

use Exception;
use icy2003\php\I;
use icy2003\php\icomponents\file\FileConstants;
use icy2003\php\icomponents\file\LocalFile;
use icy2003\php\ihelpers\Strings;

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
        try {
            $local = new LocalFile();
            $local->spl($remoteFile . 'x');
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

    public function testGetATime()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertEquals($local->getATime($file), fileatime(I::getAlias($file)));
    }

    public function testGetBasename()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertEquals($local->getBasename($file), 'data.txt');
    }

    public function testGetCTime()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertEquals($local->getCTime($file), filectime(I::getAlias($file)));
    }

    public function testGetExtension()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertEquals($local->getExtension($file), 'txt');
    }

    public function testGetFilename()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertEquals($local->getFilename($file), 'data');
    }

    public function testGetMTime()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertEquals($local->getMTime($file), filemtime(I::getAlias($file)));
    }

    public function testGetDirname()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertEquals($local->getDirname($file), Strings::replace(pathinfo(I::getAlias($file), PATHINFO_DIRNAME), ['\\' => '/']));
    }

    public function testGetPerms()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertEquals($local->getPerms($file), fileperms(I::getAlias($file)));
    }

    public function testGetFilesize()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertEquals($local->getFilesize($file), filesize(I::getAlias($file)));
    }

    public function testGetType()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertEquals($local->getType($file), 'file');
    }

    public function testIsDir()
    {
        $local = new LocalFile();
        $this->tester->assertFalse($local->isDir('@icy2003/php_tests/_data/data.txt'));
        $this->tester->assertTrue($local->isDir('@icy2003/php_tests/_data'));
    }

    public function testIsDot()
    {
        $local = new LocalFile();
        $this->tester->assertFalse($local->isDot('@icy2003/php_tests/_data/data.txt'));
        $this->tester->assertTrue($local->isDot('@icy2003/php_tests/_data/.'));
    }

    public function testIsFile()
    {
        $local = new LocalFile();
        $this->tester->assertTrue($local->isFile('@icy2003/php_tests/_data/data.txt'));
        $this->tester->assertFalse($local->isFile('@icy2003/php_tests/_data'));
        $this->tester->assertTrue($local->isFile('https://mirrors.aliyun.com/composer/composer.phar'));
        $this->tester->assertFalse($local->isFile('https://mirrors.aliyun.com/composer/composer.pharx'));
    }

    public function testIsLink()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertFalse($local->isLink($file));
        $this->tester->assertEquals($local->isLink($file), is_link(I::getAlias($file)));
    }

    public function testIsReadable()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertEquals($local->isReadable($file), is_readable(I::getAlias($file)));
    }

    public function testIsWritable()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertEquals($local->isWritable($file), is_writable(I::getAlias($file)));
    }

    public function testGetCommandResult()
    {
        $local = new LocalFile();
        $this->tester->assertNotEmpty($local->getCommandResult('php -v'));
    }

    public function testGetRealpath()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertEquals($local->getRealpath($file), Strings::replace(realpath(I::getAlias($file)), ["\\" => '/']));
    }

    public function testGetLists()
    {
        $local = new LocalFile();
        $dir = '@icy2003/php_tests/_data/lists';
        $lists = $local->getLists($dir, FileConstants::COMPLETE_PATH | FileConstants::RECURSIVE);
        foreach ($lists as $file) {
            if (basename($file) == 'a') {
                $this->tester->assertTrue(true);
            }
            if (basename($file) == 'a.txt') {
                $this->tester->assertTrue(true);
            }
            if (basename($file) == 'b') {
                $this->tester->assertTrue(true);
            }
            if (basename($file) == 'b.txt') {
                $this->tester->assertTrue(true);
            }
            if (basename($file) == 'c.txt') {
                $this->tester->assertTrue(true);
            }
        }
        $lists = $local->getLists($dir, FileConstants::COMPLETE_PATH_DISABLED | FileConstants::RECURSIVE);
        foreach ($lists as $file) {
            if (basename($file) == 'a') {
                $this->tester->assertTrue(true);
            }
            if (basename($file) == 'a.txt') {
                $this->tester->assertTrue(true);
            }
            if (basename($file) == 'b') {
                $this->tester->assertTrue(true);
            }
            if (basename($file) == 'b.txt') {
                $this->tester->assertTrue(true);
            }
            if (basename($file) == 'c.txt') {
                $this->tester->assertTrue(true);
            }
        }
    }

    public function testGetFileContent()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $this->tester->assertEquals($local->getFileContent($file), '1
22
333
4444
55555
666666
7777777
88888888
999999999');
    }

    public function testPutFileContent()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data2.txt';
        $local->putFileContent($file, 'aaa');
        $this->tester->assertFileExists(I::getAlias($file));
        $local->deleteFile($file);
        $local->deleteFile($file);
        $this->tester->assertTrue(true);
    }

    public function testDeleteFile()
    {
        $this->tester->assertTrue(true);
    }

    public function testUploadFile()
    {
        $local = new LocalFile();
        $local->uploadFile('xx');
        $this->tester->assertTrue(true);
    }

    public function testDownloadFile()
    {
        $remoteFile = 'https://mirrors.aliyun.com/composer/composer.phar';
        $localFile = '@icy2003/php_runtime/composer.phar';
        $local = new LocalFile();
        $local->downloadFile([$remoteFile, $localFile], true, function ($size, $total) {
            $this->tester->assertIsInt($size);
            $this->tester->assertIsInt($total);
        }, function ($spl) use ($local, $remoteFile, $localFile) {
            $this->tester->assertFileExists(I::getAlias($localFile));
            $local->downloadFile([$remoteFile, $localFile], false, null, function () use ($localFile) {
                $this->tester->assertFileExists(I::getAlias($localFile));
            });
            $local->deleteFile($localFile);
        });
    }

    public function testChmod()
    {
        $local = new LocalFile();
        $local->chmod('@icy2003/php_tests/_data', 0777, FileConstants::RECURSIVE);
        $local->chmod('@icy2003/php_tests/_data', 0777, FileConstants::RECURSIVE_DISABLED);
        $this->tester->assertTrue(true);
    }

    public function testCopyFile()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $file2 = '@icy2003/php_tests/_data/data2.txt';
        $local->copyFile($file, $file2);
        $this->tester->assertFileExists(I::getAlias($file2));
        $local->deleteFile($file2);
    }

    public function testMoveFile()
    {
        $local = new LocalFile();
        $file = '@icy2003/php_tests/_data/data.txt';
        $file2 = '@icy2003/php_tests/_data/data2.txt';
        $file3 = '@icy2003/php_tests/_data/data3.txt';
        $local->copyFile($file, $file2);
        $local->moveFile($file2, $file3);
        $this->tester->assertFileNotExists(I::getAlias($file2));
        $this->tester->assertFileExists(I::getAlias($file3));
        $local->deleteFile($file3);
    }

    public function testCopyDir()
    {
        $local = new LocalFile();
        $dir = '@icy2003/php_tests/_data/lists';
        $dir2 = '@icy2003/php_tests/_data/list2';
        $local->copyDir($dir, $dir2);
        $this->tester->assertFileExists(I::getAlias('@icy2003/php_tests/_data/list2/a/b/c.txt'));
        $local->deleteDir($dir2);
    }
}
