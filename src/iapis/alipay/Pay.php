<?php
/**
 * Class Pay
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis\alipay;

use Exception;
use icy2003\php\I;
use icy2003\php\ihelpers\Base64;
use icy2003\php\ihelpers\Charset;
use icy2003\php\ihelpers\Crypto;
use icy2003\php\ihelpers\Json;
use icy2003\php\ihelpers\Strings;

/**
 * 支付宝支付
 *
 * - 参看[支付宝支付开发文档](https://docs.open.alipay.com/)
 */
class Pay
{
    use PaySetterTrait;

    /**
     * 初始化
     *
     * @param string $appId 支付宝分配给开发者的应用ID
     * @param string $rsaPrivateKey 商户私钥
     */
    public function __construct($appId, $rsaPrivateKey)
    {
        $this->_appId = $appId;
        $this->_rsaPrivateKey = $rsaPrivateKey;
    }

    /**
     * app支付接口2.0
     *
     * - APP支付：返回的字符串需给客户端拉起支付宝
     *
     * @return static
     */
    public function pay()
    {
        if (null === I::get($this->_values, 'biz_content.total_amount')) {
            throw new Exception('缺少参数：biz_content.total_amount');
        }
        if (null === I::get($this->_values, 'biz_content.subject')) {
            throw new Exception('缺少参数：biz_content.subject');
        }
        if (null === I::get($this->_values, 'biz_content.out_trade_no')) {
            throw new Exception('缺少参数：biz_content.out_trade_no');
        }
        $values = array_filter([
            'app_id' => $this->_appId,
            'method' => 'alipay.trade.app.pay',
            'format' => I::get($this->_values, 'format'),
            'return_url' => I::get($this->_values, 'return_url'),
            'charset' => I::get($this->_values, 'charset', 'utf-8'),
            'sign_type' => I::get($this->_values, 'sign_type', 'RSA2'),
            'timestamp' => I::get($this->_values, 'timestamp', date('Y-m-d H:i:s')),
            'version' => '1.0',
            'notify_url' => I::get($this->_values, 'notify_url'),
            'app_auth_token' => I::get($this->_values, 'app_auth_token'),
            'biz_content' => Json::encode(I::get($this->_values, 'biz_content', [])),
        ]);
        $values['sign'] = $this->getSign($values);
        $this->_result = http_build_query($values);
        return $this;
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
        // 不得不吐槽，支付宝这部分代码写的比微信的烂
        $array = [];
        $params = array_filter($params);
        foreach ($params as $key => $value) {
            if ('sign' !== $key && false === Strings::isStartsWith($value, '@')) {
                $array[] = $key . '=' . Charset::convertTo($value, I::get($this->_values, 'charset', 'utf-8'));
            }
        }
        $string = implode('&', $array);
        $crypto = new Crypto();
        $crypto->setPair([null, $this->_rsaPrivateKey]);
        $signType = I::get($this->_values, 'sign_type', 'RSA2');
        if ('RSA' === $signType) {
            $sign = $crypto->getSignature($string, OPENSSL_ALGO_SHA1);
        } elseif ('RSA2' === $signType) {
            $sign = $crypto->getSignature($string, OPENSSL_ALGO_SHA256);
        } else {
            throw new Exception("不支持的签名类型");
        }
        $sign = Base64::encode($sign);
        return $sign;
    }

    /**
     * 返回刚刚调用过的微信接口的结果
     *
     * @return array
     */
    public function getRes()
    {
        return $this->_result;
    }

}
