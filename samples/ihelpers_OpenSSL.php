<?php

include __DIR__.'/../I.php';

use icy2003\ihelpers\OpenSSL;

$sign = new OpenSSL();
$sign->confPath = 'E:/develop/phpStudy/Apache/conf/openssl.cnf';
$sign->signType = 'RSA';
$res = $sign->newKey()->sign('content');

var_dump($res);
