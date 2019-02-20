<?php

include __DIR__ . '/../I.php';

use icy2003\ihelpers\FileIO;

// FileIO::createDir(dirname(__FILE__).'/a');
// FileIO::createFile(dirname(__FILE__).'/a.php');

$spl = FileIO::create()->loadDir(dirname(__FILE__), null, 'ihelpers_*')->spl();

foreach ($spl as $d) {
    /**
     * @var \DirectoryIterator $d
     */
    echo $d->getPathname(), PHP_EOL;
    echo $d->getBasename(), PHP_EOL;
}
