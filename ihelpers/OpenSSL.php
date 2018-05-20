<?php

namespace icy2003\ihelpers;

class OpenSSL
{
    public $publicKey;
    public $privateKey;
    public $privateKeyPath;
    public $sign;
    public $signType = 'RSA2';
    private $signConf = [
        'RSA' => [
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ],
        'RSA2' => [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ],
    ];
    private $signAlg = [
        'RSA' => OPENSSL_ALGO_SHA1,
        'RSA2' => OPENSSL_ALGO_SHA256,
    ];
    public $confPath;

    public function sign($data)
    {
        if (!empty($this->privateKeyPath)) {
            $this->privateKey = openssl_pkey_get_private(file_get_contents($this->privateKeyPath));
        }
        if (!$this->privateKey) {
            throw new IException('您使用的私钥格式错误，请检查RSA私钥配置');
        }
        openssl_sign($data, $this->sign, $this->privateKey, $this->signAlg[$this->signType]);
        $this->privateKeyPath && openssl_free_key($this->privateKey);

        return $this;
    }

    public function newKey()
    {
        if (empty($this->confPath)) {
            throw new IException('需要配置 openssl.cnf');
        }
        $config = $this->signConf[$this->signType];
        $config['config'] = $this->confPath;
        $newKeyPair = openssl_pkey_new($config);
        openssl_pkey_export($newKeyPair, $this->privateKey, null, $config);
        $detail = openssl_pkey_get_details($newKeyPair);
        $this->publicKey = $detail['key'];

        return $this;
    }
}
