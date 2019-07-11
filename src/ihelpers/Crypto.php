<?php
/**
 * Class Crypto
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

use Exception;
use icy2003\php\I;

/**
 * 加解密
 *
 * 对 openssl 扩展的封装
 */
class Crypto
{
    /**
     * 生成新密钥的配置
     *
     * @var array
     */
    protected $_config;

    /**
     * 初始化
     *
     * - 检测扩展
     * - 给密钥配置以默认值，默认为 RSA-SHA256
     */
    public function __construct()
    {
        if (false === extension_loaded('openssl')) {
            throw new Exception("请安装 php_openssl 扩展");
        }
        $this->_config = [
            'digest_alg' => 'SHA256',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'config' => I::getAlias('@icy2003/php/openssl.cnf'),
        ];
    }

    /**
     * 设置摘要算法或签名哈希算法
     *
     * @param string $method
     *
     * @return static
     */
    public function setDigestAlg($method)
    {
        $methods = openssl_get_md_methods();
        if (!in_array($method, $methods)) {
            throw new Exception("不合法的摘要算法");
        }
        $this->_config['digest_alg'] = $method;
        return $this;
    }

    /**
     * 指定应该使用多少位来生成私钥
     *
     * @param integer $bit
     *
     * @return static
     */
    public function setPrivateKeyBits($bit)
    {
        $this->_config['private_key_bits'] = $bit;
        return $this;
    }

    /**
     * 选择在创建CSR时应该使用哪些扩展
     *
     * - 可选值：
     *      - OPENSSL_KEYTYPE_DSA
     *      - OPENSSL_KEYTYPE_DH
     *      - OPENSSL_KEYTYPE_RSA
     *      - OPENSSL_KEYTYPE_EC
     *
     * @param integer $privateKeyType
     *
     * @return static
     */
    public function setPrivateKeyType($privateKeyType)
    {
        $types = [
            OPENSSL_KEYTYPE_DSA, OPENSSL_KEYTYPE_DH, OPENSSL_KEYTYPE_RSA, OPENSSL_KEYTYPE_EC,
        ];
        if (!in_array($privateKeyType, $types)) {
            throw new Exception("不合法的密钥扩展名");
        }
        $this->_config['private_key_type'] = $privateKeyType;
        return $this;
    }

    /**
     * 自定义 openssl.conf 文件的路径
     *
     * @param string $config 支持别名
     *
     * @return static
     */
    public function setConfig($config = null)
    {
        null === $config && $config = I::get($this->_config, 'config');
        if (false === file_exists($configPath = I::getAlias($config))) {
            throw new Exception("openssl.cnf 文件不存在");
        }
        $this->_config['config'] = $configPath;
        return $this;
    }

    /**
     * pem 格式的私钥
     *
     * @var string
     */
    protected $_pemPrivate;
    /**
     * pem 格式的公钥
     *
     * @var string
     */
    protected $_pemPublic;
    /**
     * 证书密码
     *
     * @var string
     */
    protected $_password;

    /**
     * 设置证书密码
     *
     * @param string $password
     *
     * @return static
     */
    public function setPassword($password)
    {
        $this->_password = $password;
        return $this;
    }

    /**
     * 生成密钥对
     *
     * @return static
     */
    public function generatePair()
    {
        $privateKeyRes = openssl_pkey_new($this->_config);
        openssl_pkey_export($privateKeyRes, $this->_pemPrivate, $this->_password, $this->_config);
        $detail = openssl_pkey_get_details($privateKeyRes);
        $this->_pemPublic = $detail['key'];
        return $this;
    }

    /**
     * 获取密钥对
     *
     * @param boolean $toCompact 是否压缩在一起（去头尾，删除换行），默认 false，即不压缩
     *
     * @return array
     */
    public function getPair($toCompact = false)
    {
        if (false === $toCompact) {
            return [$this->_pemPublic, $this->_pemPrivate];
        } else {
            $publicKeyArr = array_filter(explode("\n", $this->_pemPublic));
            $privateKeyArr = array_filter(explode("\n", $this->_pemPrivate));
            return [implode('', array_slice($publicKeyArr, 1, -1)), implode('', array_slice($privateKeyArr, 1, -1))];
        }
    }

    /**
     * 设置密钥对
     *
     * - 给定数组必须为只包含两个元素的一维数组，第一个为公钥，第二个为私钥
     * - 给 false 则表示不提供该值
     *
     * @param array $pair 第一项为公钥，第二项为私钥，自动检测是否被压缩
     *
     * @return static
     */
    public function setPair($pair)
    {
        if (2 !== Arrays::count($pair)) {
            throw new Exception("请给只包含两个元素的一维数组，第一个为公钥，第二个为私钥。如果不想给，请填 false");
        }
        if (false !== $pair[0]) {
            $this->_pemPublic = $pair[0];
            if (false === Strings::isContains($this->_pemPublic, "\n")) {
                $this->_pemPublic = "-----BEGIN PUBLIC KEY-----\n" .
                chunk_split($this->_pemPublic, 64, "\n") .
                    '-----END PUBLIC KEY-----';
            }
        }
        if (false !== $pair[1]) {
            $this->_pemPrivate = $pair[1];
            if (false === Strings::isContains($this->_pemPrivate, "\n")) {
                $this->_pemPrivate = "-----BEGIN PRIVATE KEY-----\n" .
                chunk_split($this->_pemPrivate, 64, "\n") .
                    '-----END PRIVATE KEY-----';
            }
        }

        return $this;
    }

    /**
     * 获取被公钥加密的值
     *
     * - 加密后由于无法正常显示，因此会返回 base64 后的结果
     *
     * @param string $data
     *
     * @return string
     */
    public function getPublicEncrypt($data)
    {
        if (null === $this->_pemPublic) {
            throw new Exception("请使用 setPair 提供公钥");
        }
        openssl_public_encrypt($data, $encrypted, $this->_pemPublic);
        return Base64::encode($encrypted);
    }

    /**
     * 获取被私钥加密后，公钥解密的值
     *
     * @param string $encrypted 被私钥加密后的密文
     *
     * @return string
     */
    public function getPublicDecrypt($encrypted)
    {
        if (null === $this->_pemPublic) {
            throw new Exception("请使用 setPair 提供公钥");
        }
        $encrypted = Base64::isBase64($encrypted) ? Base64::decode($encrypted) : $encrypted;
        openssl_public_decrypt($encrypted, $decrypted, $this->_pemPublic);
        return $decrypted;
    }

    /**
     * 获取被私钥加密的值
     *
     * - 加密后由于无法正常显示，因此会返回 base64 后的结果
     *
     * @param string $data
     *
     * @return static
     */
    public function getPrivateEncrypt($data)
    {
        if (null === $this->_pemPrivate) {
            throw new Exception("请使用 setPair 提供私钥");
        }
        openssl_private_encrypt($data, $encrypted, $this->_pemPrivate);
        return Base64::encode($encrypted);
    }

    /**
     * 获取被公钥加密后，私钥解密的值
     *
     * @param string $encrypted 被公钥加密后的密文
     *
     * @return string
     */
    public function getPrivateDecrypt($encrypted)
    {
        if (null === $this->_pemPrivate) {
            throw new Exception("请使用 setPair 提供私钥");
        }
        $encrypted = Base64::isBase64($encrypted) ? Base64::decode($encrypted) : $encrypted;
        openssl_private_decrypt($encrypted, $decrypted, $this->_pemPrivate);
        return $decrypted;
    }

    /**
     * 在证书中使用的专有名称或主题字段
     *
     * @var array
     */
    protected $_dn = [];

    /**
     * 设置在证书中使用的专有名称或主题字段
     *
     * @param string $key
     * @param string $value
     *
     * @return static
     */
    public function setDn($key, $value)
    {
        $this->_dn[$key] = $value;
        return $this;
    }

    /**
     * 国家名
     *
     * @param string $countryName
     *
     * @return static
     */
    public function setDnCountryName($countryName)
    {
        return $this->setDn('countryName', $countryName);
    }

    /**
     * 省份
     *
     * @param string $stateOrProvinceName
     *
     * @return static
     */
    public function setDnStateOrProvinceName($stateOrProvinceName)
    {
        return $this->setDn('stateOrProvinceName', $stateOrProvinceName);
    }

    /**
     * 城市
     *
     * @param string $localityName
     *
     * @return static
     */
    public function setDnLocalityName($localityName)
    {
        return $this->setDn('localityName', $localityName);
    }

    /**
     * 注册人姓名
     *
     * @param string $organizationName
     *
     * @return static
     */
    public function setDnOrganizationName($organizationName)
    {
        return $this->setDn('organizationName', $organizationName);
    }

    /**
     * 组织名称
     *
     * @param string $organizationalUnitName
     *
     * @return static
     */
    public function setDnOrganizationalUnitName($organizationalUnitName)
    {
        return $this->setDn('organizationalUnitName', $organizationalUnitName);
    }

    /**
     * 公共名称
     *
     * @param string $commonName
     *
     * @return static
     */
    public function setDnCommonName($commonName)
    {
        return $this->setDn('commonName', $commonName);
    }

    /**
     * 邮箱
     *
     * @param string $emailAddress
     *
     * @return static
     */
    public function setDnEmailAddress($emailAddress)
    {
        return $this->setDn('emailAddress', $emailAddress);
    }

    /**
     * pem 格式的整数
     *
     * @var string
     */
    protected $_pemCert;
    /**
     * pem 格式 csr（证书请求）
     *
     * @var string
     */
    protected $_pemCsr;
    /**
     * p12 格式的私钥
     *
     * @var string
     */
    protected $_p12Private;

    /**
     * 生成证书
     *
     * - 如若没有设置私钥，则默认生成一对密钥对，用 setPair 设置私钥
     *
     * @return static
     */
    public function generateCert()
    {
        if (null === $this->_pemPrivate) {
            $this->generatePair();
        }
        // 生成一个 CSR 资源
        $csr = openssl_csr_new($this->_dn, $this->_pemPrivate, $this->_config);
        // 用另一个证书签署 CSR (或者本身) 并且生成一个证书
        $x509 = openssl_csr_sign($csr, null, $this->_pemPrivate, 365, $this->_config);
        // 将 x509 以PEM编码的格式导出
        openssl_x509_export($x509, $this->_pemCert);
        openssl_csr_export($csr, $this->_pemCsr);
        // 将 PKCS#12 兼容证书存储文件导出到变量
        openssl_pkcs12_export($x509, $this->_p12Private, $this->_pemPrivate, $this->_password);
        return $this;
    }

    /**
     * 返回生成的证书参数
     *
     * - pemPublic：pem 格式的公钥
     * - pemPrivate：pem 格式的私钥
     * - pemCert：pem 格式的证书
     * - pemCsr：pem 格式的CSR（证书请求）
     * - p12Private：p12 格式的私钥
     *
     * @return array
     */
    public function getCert()
    {
        return [
            'pemPublic' => $this->_pemPublic,
            'pemPrivate' => $this->_pemPrivate,
            'pemCert' => $this->_pemCert,
            'pemCsr' => $this->_pemCsr,
            'p12Private' => $this->_p12Private,
        ];
    }

    /**
     * 获取用私钥生成的签名
     *
     * @param string $data 待签名数据
     * @param integer|string $signType 见[openssl_sign](https://www.php.net/manual/zh/function.openssl-sign.php)
     *
     * @return string
     */
    public function getSignature($data, $signType = OPENSSL_ALGO_SHA256)
    {
        if (null === $this->_pemPrivate) {
            throw new Exception("请使用 setPair 提供私钥");
        }
        openssl_sign($data, $signature, $this->_pemPrivate, $signType);
        return $signature;
    }

    /**
     * 校验签名
     *
     * @param string $data
     * @param string $signature
     * @param integer|string $signType
     *
     * @return boolean
     */
    public function isVerify($data, $signature, $signType = OPENSSL_ALGO_SHA256)
    {
        if (null === $this->_pemPublic) {
            throw new Exception("请使用 setPair 提供公钥");
        }
        return openssl_verify($data, $signature, $this->_pemPublic, $signType);
    }
}
