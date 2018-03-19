<?php

namespace icy2003\isdk\RongCloud;

use icy2003\ihelpers\Curl;

class RongCloud
{
    protected $curl = null;
    protected $appOptions = [];

    public function __construct($appKey, $appSecret)
    {
        $nonce = mt_rand();
        $timeStamp = time();
        $sign = sha1($appSecret.$nonce.$timeStamp);
        $this->appOptions = [
            'RC-App-Key:'.$appKey,
            'RC-Nonce:'.$nonce,
            'RC-Timestamp:'.$timeStamp,
            'RC-Signature:'.$sign,
        ];
        $this->curl = new Curl();
    }

    public function get($url, $params = [])
    {
        $res = $this->curl
                    ->setOption(CURLOPT_HTTPHEADER, $this->appOptions)
                    ->get($url, $params);

        return $res;
    }

    public function post($url, $postBody, $params = [])
    {
        $res = $this->curl
                    ->setOption(CURLOPT_HTTPHEADER, $this->appOptions)
                    ->post($url, $postBody, $params);

        return $res;
    }
}
