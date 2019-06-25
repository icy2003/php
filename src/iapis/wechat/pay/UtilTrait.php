<?php
/**
 * Trait UtilTrait
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis\wechat\pay;

use Exception;
use icy2003\php\I;
use icy2003\php\ihelpers\Request;

/**
 * 工具
 */
trait UtilTrait
{

    /**
     * 商户号
     *
     * @var string
     */
    protected $_mchId;

    /**
     * 应用ID
     *
     * @var string
     */
    protected $_appId;

    /**
     * 密钥
     *
     * @var string
     */
    protected $_apiKey;

    /**
     * 证书路径
     *
     * @var string
     */
    protected $_certPath;

    /**
     * 设置证书路径
     *
     * @param string $certPath
     *
     * @return void
     */
    public function setCertPath($certPath)
    {
        $this->_certPath = $certPath;
    }

    /**
     * 证书密钥路径
     *
     * @var string
     */
    protected $_certKeyPath;

    /**
     * 设置证书密钥路径
     *
     * @param string $certKeyPath
     *
     * @return void
     */
    public function setCertKeyPath($certKeyPath)
    {
        $this->_certKeyPath = $certKeyPath;
    }

    /**
     * 获取终端 IP
     *
     * @return string
     */
    public function getIp()
    {
        return (new Request())->getRemoteIP();
    }

    /**
     * 生成签名
     *
     * @param array $params 签名参数
     *
     * @return string
     */
    public function getSign($params)
    {
        ksort($params);
        $arr = [];
        foreach ($params as $key => $value) {
            if ($key != 'sign' && !empty($value)) {
                $arr[] = $key . '=' . $value;
            }
        }
        $arr[] = 'key=' . $this->_apiKey;
        $string = implode('&', $arr);
        $method = I::get($params, 'sign_type', 'MD5');
        if ('MD5' === $method) {
            $string = md5($string);
        } elseif ('HMAC-SHA256' === $method) {
            $string = hash_hmac("sha256", $string, $this->_apiKey);
        } else {
            throw new Exception("签名类型不支持！");
        }
        return strtoupper($string);
    }
}
