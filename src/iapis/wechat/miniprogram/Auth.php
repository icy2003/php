<?php

namespace icy2003\php\iapis\wechat\miniprogram;

use icy2003\php\I;
use icy2003\php\iapis\Api;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;

class Auth extends Api
{

    public function __construct($appid, $secret)
    {
        $this->setOption('appid', $appid);
        $this->setOption('secret', $secret);
    }

    public function isSuccess()
    {
        if (0 === I::get($this->_result, 'errcode', 0)) {
            return true;
        }
        return false;
    }

    public function getError()
    {
        return [
            'errcode' => I::get($this->_result, 'errcode', 0),
            'errmsg' => I::get($this->_result, 'errmsg', ''),
        ];
    }

    public function getAccessToken()
    {
        $this->setOption('grant_type', 'client_credential');
        $this->_result = Json::decode(Http::get('https://api.weixin.qq.com/cgi-bin/token', $this->filterOptions([
            'grant_type', 'appid', 'secret',
        ])));
        $this->_toArrayCall = function ($array) {
            return Arrays::columns1($array, ['access_token', 'expires_in']);
        };

        return $this;
    }

    public function code2Session($code)
    {
        $this->setOption('grant_type', 'authorization_code');
        $this->setOption('js_code', $code);
        $this->_result = Json::decode(Http::get('https://api.weixin.qq.com/sns/jscode2session', $this->filterOptions([
            'appid', 'secret', 'js_code', 'grant_type',
        ])));
        // 这个接口返回没有 errcode
        $this->_toArrayCall = function ($array) {
            return Arrays::columns1($array, ['openid', 'session_key', 'unionid']);
        };

        return $this;
    }
}
