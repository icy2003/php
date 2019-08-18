<?php
/**
 * Class Pay
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis\wechat;

use Exception;
use icy2003\php\C;
use icy2003\php\I;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Header;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Request;
use icy2003\php\ihelpers\Strings;
use icy2003\php\ihelpers\Url;
use icy2003\php\ihelpers\Xml;

/**
 * Pay 支付
 *
 * - 参看[微信支付开发文档](https://pay.weixin.qq.com/wiki/doc/api/index.html)
 */
class Pay
{

    use PaySetterTrait;

    /**
     * 初始化
     *
     * @param string $mchid 商户号。微信支付分配的商户号
     * @param string $appid 应用ID。微信开放平台审核通过的应用APPID（请登录open.weixin.qq.com查看，注意与公众号的APPID不同）
     * @param string $apiKey 密钥。key设置路径：微信商户平台(pay.weixin.qq.com)-->账户设置-->API安全-->密钥设置
     */
    public function __construct($mchid, $appid, $apiKey)
    {
        $this->_mchId = $mchid;
        if (null === $this->_mchId) {
            throw new Exception("缺少商户号");
        }
        $this->_appId = $appid;
        if (null === $this->_appId) {
            throw new Exception("缺少应用 ID");
        }
        $this->_apiKey = $apiKey;
        if (null === $this->_apiKey) {
            throw new Exception("缺少密钥");
        }
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

    /**
     * 支付类型：APP
     */
    const TRADE_TYPE_APP = 'APP';
    /**
     * 支付类型：JSAPI
     */
    const TRADE_TYPE_JSAPI = 'JSAPI';
    /**
     * 支付类型：Native
     */
    const TRADE_TYPE_NATIVE = 'NATIVE';
    /**
     * 支付类型：H5
     */
    const TRADE_TYPE_H5 = 'MWEB';
    /**
     * 支付类型：付款码
     */
    const TRADE_TYPE_MICROPAY = 'MICROPAY';

    /**
     * 统一下单
     *
     * - 商户系统先调用该接口在微信支付服务后台生成预支付交易单，返回正确的预支付交易会话标识后再在APP里面调起支付
     *
     * @return static
     */
    public function pay()
    {
        if (null === ($body = I::get($this->_values, 'body'))) {
            throw new Exception('缺少统一支付接口必填参数：body');
        }
        if (null === ($outTradeNo = I::get($this->_values, 'out_trade_no'))) {
            throw new Exception('缺少统一支付接口必填参数：out_trade_no');
        }
        if (null === ($totalFee = I::get($this->_values, 'total_fee'))) {
            throw new Exception('缺少统一支付接口必填参数：total_fee');
        }
        if (null === ($notifyUrl = I::get($this->_values, 'notify_url'))) {
            throw new Exception('缺少统一支付接口必填参数：notify_url');
        }
        if (null === ($tradeType = I::get($this->_values, 'trade_type'))) {
            throw new Exception('缺少统一支付接口必填参数：trade_type');
        }
        $values = array_filter([
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'device_info' => I::get($this->_values, 'device_info'),
            'nonce_str' => Strings::random(),
            'sign_type' => I::get($this->_values, 'sign_type'),
            'body' => $body,
            'detail' => I::get($this->_values, 'detail'),
            'attach' => I::get($this->_values, 'attach'),
            'out_trade_no' => $outTradeNo,
            'fee_type' => I::get($this->_values, 'fee_type'),
            'total_fee' => $totalFee,
            'spbill_create_ip' => $this->getIp(),
            'time_start' => I::get($this->_values, 'time_start'),
            'time_expire' => I::get($this->_values, 'time_expire'),
            'goods_tag' => I::get($this->_values, 'goods_tag'),
            'notify_url' => $notifyUrl,
            'trade_type' => $tradeType,
            'limit_pay' => I::get($this->_values, 'limit_pay'),
            'receipt' => I::get($this->_values, 'receipt'),
        ]);

        if ('NATIVE' === $tradeType) {
            if (null === ($productId = I::get($this->_values, 'product_id'))) {
                throw new Exception('缺少统一支付接口必填参数：product_id');
            } else {
                $values['product_id'] = $productId;
            }
        } elseif ('JSAPI' === $tradeType) {
            if (null === ($openId = I::get($this->_values, 'openid'))) {
                throw new Exception('缺少统一支付接口必填参数：openid');
            } else {
                $values['openid'] = $openId;
            }
        } elseif ('MWEB' === $tradeType) {
            if (null === ($sceneInfo = I::get($this->_values, 'scene_info'))) {
                throw new Exception('缺少统一支付接口必填参数：scene_info');
            } else {
                $values['scene_info'] = $sceneInfo;
            }
        }
        $values['sign'] = $this->getSign($values);
        $responseXml = Http::body('https://api.mch.weixin.qq.com/pay/unifiedorder', Xml::fromArray($values));
        $this->_result = Xml::toArray($responseXml);
        return $this;
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

    /**
     * 交易成功！
     *
     * - 只有交易成功有意义
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return 'SUCCESS' === I::get($this->_result, 'return_code');
    }

    /**
     * 返回用于拉起微信支付用的前端参数
     *
     * @return array
     */
    public function getCallArray()
    {
        C::assertTrue($this->isSuccess(), (string) I::get($this->_result, 'return_msg'));
        $array = [];
        if ('APP' === I::get($this->_values, 'trade_type')) {
            $array = [
                'appid' => $this->_appId,
                'partnerid' => $this->_mchId,
                'prepayid' => I::get($this->_result, 'prepay_id'),
                'package' => 'Sign=WXPay',
                'noncestr' => Strings::random(),
                'timestamp' => time(),
            ];
            $array['sign'] = $this->getSign($array);
        }
        if ('MWEB' === I::get($this->_values, 'trade_type')) {
            $array = [
                'mweb_url' => I::get($this->_result, 'mweb_url'),
            ];
        }
        return $array;
    }

    /**
     * 支付结果通知以及退款结果通知的数据处理
     *
     * - 如果交易成功，并且签名校验成功，返回数据
     *
     * @return array
     */
    public function getNotifyArray()
    {
        $xml = (new Request())->getRawBody();
        $array = Xml::toArray($xml);
        C::assertTrue('SUCCESS' === I::get($array, 'return_code') && 'SUCCESS' === I::get($array, 'result_code'), (string) I::get($array, 'return_msg'));
        $temp = $array;
        $sign = $temp['sign'];
        unset($temp['sign']);
        if ($this->getSign($temp) == $sign) {
            return $array;
        }
        return [];
    }

    /**
     * 返回通知成功时发送给微信的 XML
     *
     * @return string
     */
    public function getNotifyReturn()
    {
        return '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
    }

    /**
     * self::getNotifyArray 和 self::getNotifyReturn 的结合：通知为交易成功时，$callback 为 true，则输出成功给微信
     *
     * @param callback $callback 回调函数，true 或设置回调则输出成功，回调函数提供了微信给的通知数组 $array
     *
     * @return void
     */
    public function notify($callback = null)
    {
        $array = $this->getNotifyArray();
        if (!empty($array)) {
            if (null === $callback || true === I::call($callback, [$array])) {
                Header::xml();
                echo $this->getNotifyReturn();
                die;
            }
        }
    }

    /**
     * 该接口提供所有微信支付订单的查询，商户可以通过该接口主动查询订单状态
     *
     * @return static
     */
    public function orderQuery()
    {
        $values = array_filter([
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'nonce_str' => Strings::random(),
            'transaction_id' => I::get($this->_values, 'transaction_id'),
            'out_trade_no' => I::get($this->_values, 'out_trade_no'),
        ]);
        if (false === Arrays::keyExistsSome(['transaction_id', 'out_trade_no'], $values)) {
            throw new Exception('transaction_id 和 out_trade_no 必须二选一');
        }
        $values['sign'] = $this->getSign($values);
        $responseBody = Http::body('https://api.mch.weixin.qq.com/pay/orderquery', Xml::fromArray($values));
        $this->_result = Xml::toArray($responseBody);
        return $this;
    }

    /**
     * 关闭订单
     *
     * @return static
     */
    public function closeOrder()
    {
        if (null === ($outTradeNo = I::get($this->_values, 'out_trade_no'))) {
            throw new Exception('缺少必填参数：out_trade_no');
        }
        $values = [
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'out_trade_no' => $outTradeNo,
            'nonce_str' => Strings::random(),
        ];
        $values['sign'] = $this->getSign($values);
        $responseBody = Http::body('https://api.mch.weixin.qq.com/pay/closeorder', Xml::fromArray($values));
        $this->_result = Xml::toArray($responseBody);
        return $this;
    }

    /**
     * 申请退款
     *
     * @return static
     */
    public function refund()
    {
        if (null === $this->_certPath) {
            throw new Exception('请使用 setCertPath 提供证书路径');
        }
        if (null === $this->_certKeyPath) {
            throw new Exception('请使用 setCertKeyPath 提供证书密钥路径');
        }
        $this->setOutRefundNo();
        $this->setRefundFee();
        $values = array_filter([
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'nonce_str' => Strings::random(),
            'sign_type' => I::get($this->_values, 'sign_type'),
            'transaction_id' => I::get($this->_values, 'transaction_id'),
            'out_trade_no' => I::get($this->_values, 'out_trade_no'),
            'out_refund_no' => I::get($this->_values, 'out_refund_no'),
            'total_fee' => I::get($this->_values, 'total_fee'),
            'refund_fee' => I::get($this->_values, 'refund_fee'),
            'refund_fee_type' => I::get($this->_values, 'refund_fee_type'),
            'refund_desc' => I::get($this->_values, 'refund_desc'),
            'refund_account' => I::get($this->_values, 'refund_account'),
            'notify_url' => I::get($this->_values, 'notify_url'),
        ]);
        if (false === Arrays::keyExistsSome(['transaction_id', 'out_trade_no'], $values)) {
            throw new Exception('transaction_id 和 out_trade_no 必须二选一');
        }
        $values['sign'] = $this->getSign($values);
        $responseBody = Http::body('https://api.mch.weixin.qq.com/secapi/pay/refund', Xml::fromArray($values), [], [
            'cert' => $this->_certPath,
            'ssl_key' => $this->_certKeyPath,
        ]);
        $this->_result = Xml::toArray($responseBody);
        return $this;
    }

    /**
     * 查询退款
     *
     * @return static
     */
    public function refundQuery()
    {
        $values = array_filter([
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'nonce_str' => Strings::random(),
            'sign_type' => I::get($this->_values, 'sign_type'),
            'transaction_id' => I::get($this->_values, 'transaction_id'),
            'out_trade_no' => I::get($this->_values, 'out_trade_no'),
            'out_refund_no' => I::get($this->_values, 'out_refund_no'),
            'refund_id' => I::get($this->_values, 'refund_id'),
            'offset' => I::get($this->_values, 'offset'),
        ]);
        if (false === Arrays::keyExistsSome(['transaction_id', 'out_trade_no', 'out_refund_no', 'refund_id'], $values)) {
            throw new Exception('transaction_id、out_trade_no、out_refund_no 和 refund_id 必须四选一');
        }
        $values['sign'] = $this->getSign($values);
        $responseBody = Http::body('https://api.mch.weixin.qq.com/pay/refundquery', Xml::fromArray($values));
        $this->_result = Xml::toArray($responseBody);
        return $this;
    }

    /**
     * 下载对账单
     *
     * @return static
     */
    public function downloadBill()
    {
        if (null === ($billDate = I::get($this->_values, 'bill_date'))) {
            throw new Exception('缺少 bill_date 参数！');
        }
        $values = array_filter([
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'nonce_str' => Strings::random(),
            'bill_date' => $billDate,
            'bill_type' => I::get($this->_values, 'bill_type'),
            'tar_type' => I::get($this->_values, 'tar_type'),
        ]);
        $values['sign'] = $this->getSign($values);
        $responseBody = Http::body('https://api.mch.weixin.qq.com/pay/downloadbill', Xml::fromArray($values));
        $this->_result = Xml::toArray($responseBody);
        return $this;
    }

    /**
     * 下载资金账单
     *
     * @return static
     */
    public function downloadFundFlow()
    {
        if (null === ($accoutType = I::get($this->_values, 'account_type'))) {
            throw new Exception('缺少 account_type 参数！');
        }
        if (null === ($billDate = I::get($this->_values, 'bill_date'))) {
            throw new Exception('缺少 bill_date 参数！');
        }
        if (null === $this->_certPath) {
            throw new Exception('请使用 setCertPath 提供证书路径');
        }
        if (null === $this->_certKeyPath) {
            throw new Exception('请使用 setCertKeyPath 提供证书密钥路径');
        }
        $values = array_filter([
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'nonce_str' => Strings::random(),
            'sign_type' => 'HMAC-SHA256',
            'bill_date' => $billDate,
            'account_type' => $accoutType,
            'tar_type' => I::get($this->_values, 'tar_type'),
        ]);
        $values['sign'] = $this->getSign($values);
        $responseBody = Http::body('https://api.mch.weixin.qq.com/pay/downloadfundflow', Xml::fromArray($values), [], [
            'cert' => $this->_certPath,
            'ssl_key' => $this->_certKeyPath,
        ]);
        $this->_result = Xml::toArray($responseBody);
        return $this;
    }

    /**
     * 交易保障
     *
     * @todo 我不知道这货干嘛用的
     *
     * @return false
     */
    public function report()
    {
        return false;
    }

    /**
     * 转换短链接
     *
     * @todo 测试没通过
     *
     * @return static
     */
    public function shortUrl()
    {
        C::assertTrue(null !== ($longUrl = (string) I::get($this->_values, 'long_url')), '缺少 long_url 参数！');
        $values = array_filter([
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'long_url' => $longUrl,
            'nonce_str' => Strings::random(),
            'sign_type' => I::get($this->_values, 'sign_type'),
        ]);
        $temp = $values;
        $temp['long_url'] = Url::encode($longUrl);
        $values['sign'] = $this->getSign($values);
        $responseBody = Http::body('https://api.mch.weixin.qq.com/tools/shorturl', Xml::fromArray($temp));
        $this->_result = Xml::toArray($responseBody);
        return $this;
    }

    /**
     * 拼接二维码地址
     *
     * - 详见[模式一](https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_4)
     *
     * @return string
     */
    public function getQrcodeUrl()
    {
        C::assertTrue(null !== ($productId = (string) I::get($this->_values, 'product_id')), '缺少 product_id 参数！');
        $values = [
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'time_stamp' => time(),
            'nonce_str' => Strings::random(),
            'product_id' => $productId,
        ];
        $values['sign'] = $this->getSign($values);
        return 'weixin://wxpay/bizpayurl?sign=' . $values['sign'] .
            '&appid=' . $values['appid'] .
            '&mch_id=' . $values['mch_id'] .
            '&product_id=' . $values['product_id'] .
            '&time_stamp=' . $values['time_stamp'] .
            '&nonce_str=' . $values['nonce_str'];
    }

    /**
     * 在统一下单之后，输出此 XML，可让扫码拉起支付
     *
     * - 详见[模式一](https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_4)
     * - 模式一主要流程：
     *      1. 商户平台设置扫码回调
     *      2. 调用 self::getQrcodeUrl 获取二维码地址，生成二维码给用户扫描支付，微信会发消息到回调地址
     *      3. 回调接收到微信消息，获取 product_id 和 openid，调用统一下单接口
     *      4. 设置 prepay_id 后调用此函数，返回给微信，即可实现微信扫码支付
     *
     * @return string
     */
    public function getQrcodeCallXml()
    {
        if (null === ($prepayId = I::get($this->_values, 'prepay_id'))) {
            throw new Exception('缺少 prepay_id 参数！');
        }
        $values = [
            'return_code' => 'SUCCESS',
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'nonce_str' => Strings::random(),
            'prepay_id' => $prepayId,
            'result_code' => 'SUCCESS',
        ];
        $values['sign'] = $this->getSign($values);
        return Xml::fromArray($values);
    }

    /**
     * 拉取订单评价数据
     *
     * @todo 此接口有问题，但我也不知道为什么
     *
     * @return static
     */
    public function batchQueryComment()
    {
        if (null === ($beginTime = I::get($this->_values, 'begin_time'))) {
            throw new Exception('缺少 begin_time 参数！');
        }
        if (null === ($endTime = I::get($this->_values, 'end_time'))) {
            throw new Exception('缺少 end_time 参数！');
        }
        if (null === ($offset = I::get($this->_values, 'offset'))) {
            throw new Exception('缺少 offset 参数！');
        }
        if (null === $this->_certPath) {
            throw new Exception('请使用 setCertPath 提供证书路径');
        }
        if (null === $this->_certKeyPath) {
            throw new Exception('请使用 setCertKeyPath 提供证书密钥路径');
        }
        $values = array_filter([
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'nonce_str' => Strings::random(),
            'sign_type' => 'HMAC-SHA256',
            'begin_time' => $beginTime,
            'end_time' => $endTime,
            'limit' => I::get($this->_values, 'limit'),
        ]);
        $values['offset'] = $offset;
        $values['sign'] = $this->getSign($values);
        $responseBody = Http::body('https://api.mch.weixin.qq.com/pay/batchquerycomment', Xml::fromArray($values), [], [
            'cert' => $this->_certPath,
            'ssl_key' => $this->_certKeyPath,
        ]);
        $this->_result = Xml::toArray($responseBody);
        return $this;
    }
}
