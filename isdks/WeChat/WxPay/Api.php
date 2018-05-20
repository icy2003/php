<?php

namespace icy2003\isdks\Wechat\WxPay;

use icy2003\ihelpers\Strings;
use icy2003\ihelpers\Curl;
use icy2003\ihelpers\Xml;
use icy2003\ihelpers\Csv;
use icy2003\isdks\Wechat\WxException;

class Api
{
    // 统一下单 @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_1
    const URL_UNIFIEDORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    // 查询订单 @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_2&index=4
    const URL_ORDERQUERY = 'https://api.mch.weixin.qq.com/pay/orderquery';

    // 关闭订单 @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_3&index=5
    const URL_CLOSEORDER = 'https://api.mch.weixin.qq.com/pay/closeorder';

    // 下载对账单 @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_6&index=8
    const URL_DOWNLOADBILL = 'https://api.mch.weixin.qq.com/pay/downloadbill';

    public $appid;
    public $mchId;
    public $key;
    public $appSecret;
    public $signType = 'MD5'; // 签名类型，目前支持 HMAC-SHA256 和 MD5，默认为 MD5
    public $deviceInfo = 'WEB'; // 终端设备号(门店号或收银设备 ID)，默认请传 WEB
    private $_data = [];
    private $_xml;
    private $_response;

    public function set($name, $value, $default = null)
    {
        $this->_data[$name] = $value ? $value : $default;

        return $this;
    }

    public function get($name)
    {
        return empty($this->_data[$name]) ? null : $this->_data[$name];
    }

    protected function requiredValidator($requiredKeyMap)
    {
        $diffKeyMap = array_diff($requiredKeyMap, array_keys($this->_data));
        if (!empty($diffKeyMap)) {
            throw new WxException('API 参数缺失！');
        }
    }

    public function fromArray($data)
    {
        $this->_data = $data;

        return $this;
    }

    /**
     * 统一下单.
     *
     * 商户系统先调用该接口在微信支付服务后台生成预支付交易单，返回正确的预支付交易会话标识后再在APP里面调起支付.
     *
     * @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_1
     */
    public function unifiedOrder($array = [])
    {
        if (empty($this->_data)) {
            $this->fromArray($array);
        }
        $this->requiredValidator(['body', 'out_trade_no', 'total_fee', 'notify_url', 'trade_type']);
        $this->set('appid', $this->appid);
        $this->set('mch_id', $this->mchId);
        $this->set('device_info', $this->deviceInfo);
        $this->set('sign_type', $this->signType);
        $this->set('nonce_str', Strings::random(32));
        $this->set('spbill_create_ip', !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');
        $this->set('sign', $this->sign());
        $this->toXml()->postXmlCurl(self::URL_UNIFIEDORDER)->fromXml();

        return $this->_data;
    }

    /**
     * 查询订单.
     *
     * 该接口提供所有微信支付订单的查询，商户可以通过该接口主动查询订单状态，完成下一步的业务逻辑
     *
     * @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_2&index=4
     */
    public function orderQuery($array = [])
    {
        if (empty($this->_data)) {
            $this->fromArray($array);
        }
        if (empty($this->_data['transaction_id']) && empty($this->_data['out_trade_no'])) {
            throw new WxException('transaction_id 和 out_trade_no 必须要有一个！');
        }
        $this->set('appid', $this->appid);
        $this->set('mch_id', $this->mchId);
        $this->set('nonce_str', Strings::random(32));
        $this->set('sign', $this->sign());
        $this->toXml()->postXmlCurl(self::URL_ORDERQUERY)->fromXml();

        return $this->_data;
    }

    /**
     * 关闭订单.
     *
     * 以下情况需要调用关单接口：
     * 商户订单支付失败需要生成新单号重新发起支付，要对原订单号调用关单，避免重复支付；
     * 系统下单后，用户支付超时，系统退出不再受理，避免用户继续，请调用关单接口
     *
     * @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_3&index=5
     */
    public function closeOrder($array = [])
    {
        if (empty($this->_data)) {
            $this->fromArray($array);
        }
        $this->requiredValidator(['out_trade_no']);
        $this->set('appid', $this->appid);
        $this->set('mch_id', $this->mchId);
        $this->set('nonce_str', Strings::random(32));
        $this->set('sign', $this->sign());
        $this->toXml()->postXmlCurl(self::URL_CLOSEORDER)->fromXml();

        return $this->_data;
    }

    /**
     * 下载对账单.
     *
     * 商户可以通过该接口下载历史交易清单。比如掉单、系统错误等导致商户侧和微信侧数据不一致，通过对账单核对后可校正支付状态
     *
     * @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=9_6&index=8
     */
    public function downloadBill($array = [])
    {
        if (empty($this->_data)) {
            $this->fromArray($array);
        }
        $this->requiredValidator(['bill_date', 'bill_type']);
        $this->set('appid', $this->appid);
        $this->set('mch_id', $this->mchId);
        $this->set('nonce_str', Strings::random(32));
        $this->set('sign', $this->sign());
        $this->toXml()->postXmlCurl(self::URL_DOWNLOADBILL)->fromCsv();

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
        $string = $this->httpBuildQuery($array);
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写

        return strtoupper($string);
    }

    public function httpBuildQuery($array)
    {
        $stringArray = [];
        foreach ($array as $key => $value) {
            $stringArray[] = $key.'='.$value;
        }
        $string = implode('&', $stringArray);

        return $string;
    }

    public function toXml()
    {
        $this->_xml = '<xml>';
        foreach ($this->_data as $key => $val) {
            $this->_xml .= "<{$key}>{$val}</$key>";
        }
        $this->_xml .= '</xml>';

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
        $this->checkSign();
        $xml = new Xml();
        $string = $xmlString ? $xmlString : $this->_response;
        $this->_data = $xml->fromString($string)->toArray();

        return $this;
    }

    public function fromCsv($csvString = '')
    {
        $this->checkSign();
        $csv = new Csv();
        $string = $csvString ? $csvString : $this->_response;
        $this->_data = $csv->fromString($string)->toArray();

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
        throw new WxException('签名错误！');
    }

    public function getError()
    {
        if (array_key_exists('return_code', $this->_data) && 'FAIL' == $this->_data['return_code']) {
        }
    }
}
