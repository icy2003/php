<?php

namespace icy2003\php\iapis\wechat\miniprogram;

use Exception;
use icy2003\php\iapis\Api;
use icy2003\php\ihelpers\Json;

class DataCrypt extends Api
{
    public function __construct($appid)
    {
        $this->setOption('appid', $appid);
    }

    public function decrypt($encryptedData, $iv, $sessionKey)
    {
        if (24 !== strlen($sessionKey)) {
            throw new Exception('错误的 session key');
        }
        if (24 !== strlen($iv)) {
            throw new Exception('错误的 iv');
        }
        $aesKey = base64_decode($sessionKey);
        $aesIv = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $this->_result = Json::decode(openssl_decrypt($aesCipher, 'aes-128-cbc', $aesKey, OPENSSL_RAW_DATA, $aesIv));
        if (null === $this->_result) {
            throw new Exception('错误的 encryptedData');
        }
        if ($this->_result['watermark']['appid'] !== $this->_options['appid']) {
            throw new Exception('错误的 appid');
        }
        return $this;
    }
}
