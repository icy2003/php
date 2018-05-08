<?php

namespace icy2003\isdk\Wechat\WxPay;

use icy2003\ihelpers\Strings;
use icy2003\ihelpers\Curl;
use icy2003\ihelpers\Xml;
use icy2003\isdk\Wechat\WxException;

class Api
{
    // 统一下单 @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_1
    const URL_UNIFIEDORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    protected $appid;
    protected $mchId;
    protected $key;
    protected $appSecret;
    private $_data = [];
    private $_xml = [];
    private $_response;

    public function __construct($appid, $mchId, $key, $appSecret)
    {
        $this->appid = $appid;
        $this->mchId = $mchId;
        $this->key = $key;
        $this->appSecret = $appSecret;
    }

    /**
     * 统一下单.
     *
     * 商户系统先调用该接口在微信支付服务后台生成预支付交易单，返回正确的预支付交易会话标识后再在APP里面调起支付.
     *
     * @see https://api.mch.weixin.qq.com/pay/unifiedorder
     */
    public function unifiedOrder($array)
    {
        $this->_data = $array;
        $this->_data['appid'] = $this->appid;
        $this->_data['mch_id'] = $this->mchId;
        $this->_data['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        $this->_data['nonce_str'] = Strings::random();
        $this->_data['sign'] = $this->sign();
        $this->toXml()->postXmlCurl(self::URL_UNIFIEDORDER)->fromXml();

        return $this->_data;
    }

    public function sign()
    {
        //签名步骤一：按字典序排序参数
        ksort($this->_data);
        $array = $this->_data;
        unset($array['sign']); // 不管有没有 sign
        //签名步骤二：在string后加入KEY
        $array['key'] = $this->key;
        $string = http_build_query($array);
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写

        return strtoupper($string);
    }

    public function toXml()
    {
        $xml = '<xml>';
        foreach ($this->_data as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<{$key}>{$val}</$key>";
            } else {
                $xml .= "<{$key}><![CDATA[{$val}}]]></$key>";
            }
        }
        $xml .= '</xml>';
        $this->_xml;

        return $this;
    }

    public function postXmlCurl($url, $useCert = false)
    {
        $curl = new Curl();
        $this->_response = $curl->post($url, $this->_xml);

        return $this;
    }

    public function fromXml($xmlString = '')
    {
        $xml = new Xml();
        $string = $xmlString ? $xmlString : $this->_response;
        $this->_data = $xml->fromString($string)->toArray();
        $this->checkSign();

        return $this;
    }

    public function checkSign()
    {
        $sign = $this->sign();
        if (!isset($this->_data['sign'])) {
            return true;
        }
        if ($this->_data['sign'] == $sign) {
            return true;
        }
        throw WxException('签名错误！');
    }
}
