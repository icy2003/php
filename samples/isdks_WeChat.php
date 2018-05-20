<?php

include __DIR__.'/../I.php';

use icy2003\isdks\Wechat\WxPay\Api;

$wechat = new Api();

$wechat->appid = 'wxaaaaaaaaaaaaaa'; // APPID
$wechat->mchId = '1234567890'; // 商户 ID
$wechat->key = 'asldkjfkhqpwojrpwedhgp'; // API 密钥

$res = $wechat->fromArray(['out_trade_no' => '18asdasrgjghertfhyt35243h',])->closeOrder();
var_dump($res);die;
