<?php

namespace icy2003\isdk\APICloud;

use icy2003\ihelpers\Curl;

class ApiCloudModel
{
    protected $curl = null;
    protected $appOptions = [];

    public function __construct($appId, $appKey)
    {
        $now = time();
        $this->appOptions = [
            'X-APICloud-AppId: '.$appId,
            'X-APICloud-AppKey: '.sha1($appId.'UZ'.$appKey.'UZ'.$now).'.'.$now,
            'Content-Type: application/json',
        ];
        $this->curl = new Curl();
    }

    public function get($url, $params = [])
    {
        $value = 'X-HTTP-Method-Override: GET';
        array_push($this->appOptions, $value);
        $res = $this->curl
                    ->setOption(CURLOPT_HTTPHEADER, $this->appOptions)
                    ->get($url, $params);

        return  $res;
    }

    public function post($url, $postBody, $params = [])
    {
        $value = 'X-HTTP-Method-Override: POST';
        array_push($this->appOptions, $value);
        $res = $this->curl
                    ->setOption(CURLOPT_HTTPHEADER, $this->appOptions)
                    ->post($url, $postBody, $params);

        return  $res;
    }

    public function put($url, $putBody, $params = [])
    {
        $value = 'X-HTTP-Method-Override: PUT';
        array_push($this->appOptions, $value);
        $res = $this->curl
                    ->setOption(CURLOPT_HTTPHEADER, $this->appOptions)
                    ->put($url, $putBody, $params);

        return  $res;
    }

    public function delete($url, $deleteBody, $params = [])
    {
        $value = 'X-HTTP-Method-Override: DELETE';
        array_push($this->appOptions, $value);
        $res = $this->curl
                    ->setOption(CURLOPT_HTTPHEADER, $this->appOptions)
                    ->delete($url, $deleteBody, $params);

        return  $res;
    }
}
