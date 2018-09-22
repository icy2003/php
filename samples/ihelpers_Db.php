<?php

include __DIR__.'/../I.php';

use icy2003\ihelpers\Db;

$db = Db::create()->find('table')->all();
