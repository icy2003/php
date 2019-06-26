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
use icy2003\php\I;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;
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
        $this->_appId = $appid;
        $this->_apiKey = $apiKey;
    }

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

    /**
     * 发送的数据
     *
     * @var array
     */
    protected $_values = [];

    /**
     * 接收的结果
     *
     * - 每执行一个接口都会修改此值
     *
     * @var array
     */
    protected $_result = [];

    /**
     * 设置设备号
     *
     * - 终端设备号(门店号或收银设备ID)，默认请传'WEB'
     *
     * @param string $deviceInfo
     *
     * @return static
     */
    public function setDeviceInfo($deviceInfo)
    {
        $this->_values['device_info'] = $deviceInfo;
        return $this;
    }

    /**
     * 设置签名类型
     *
     * - 目前支持HMAC-SHA256和MD5，默认为MD5
     *
     * @param string $signType
     *
     * @return static
     */
    public function setSignType($signType = 'MD5')
    {
        $this->_values['sign_type'] = $signType;
        return $this;
    }

    /**
     * 设置商品描述
     *
     * - 商品描述交易字段格式根据不同的应用场景按照以下格式：APP——需传入应用市场上的APP名字-实际商品名称，天天爱消除-游戏充值。
     *
     * @param string $body
     *
     * @return static
     */
    public function setBody($body)
    {
        $this->_values['body'] = $body;
        return $this;
    }

    /**
     * 设置商品详情
     *
     * - 商品详细描述，对于使用单品优惠的商户，该字段必须按照规范上传
     * - 详见：[单品优惠参数说明](https://pay.weixin.qq.com/wiki/doc/api/danpin.php?chapter=9_102&index=2)
     *
     * @param string $detail
     *
     * @return static
     */
    public function setDetail($detail)
    {
        $this->_values['detail'] = $detail;
        return $this;
    }

    /**
     * 设置附加数据
     *
     * - 附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
     *
     * @param string $attach
     *
     * @return static
     */
    public function setAttach($attach)
    {
        $this->_values['attach'] = $attach;
        return $this;
    }

    /**
     * 设置商户订单号
     *
     * - 商户系统内部订单号，要求32个字符内，只能是数字、大小写字母_-|*且在同一个商户号下唯一
     * - 详见：[商户订单号](https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=4_2)
     *
     * @param string $outTradeNo
     *
     * @return static
     */
    public function setOutTradeNo($outTradeNo)
    {
        $this->_values['out_trade_no'] = $outTradeNo;
        return $this;
    }

    /**
     * 设置货币类型
     *
     * - 符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表
     * - 详见：[货币类型](https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=4_2)
     *
     * @param string $feeType
     *
     * @return static
     */
    public function setFeeType($feeType = 'CNY')
    {
        $this->_values['fee_type'] = $feeType;
        return $this;
    }

    /**
     * 设置总金额
     *
     * - 订单总金额，单位为分
     * - 详见：[支付金额](https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=4_2)
     *
     * @param integer $totalFee
     *
     * @return static
     */
    public function setTotalFee($totalFee)
    {
        $this->_values['total_fee'] = $totalFee;
        return $this;
    }

    /**
     * 设置交易起始时间
     *
     * - 订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010
     * - 其他详见：[时间规则](https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=4_2)
     *
     * @param string $timeStart
     *
     * @return static
     */
    public function setTimeStart($timeStart)
    {
        $this->_values['time_start'] = $timeStart;
        return $this;
    }

    /**
     * 设置交易结束时间
     *
     * - 订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010
     * - 订单失效时间是针对订单号而言的，由于在请求支付的时候有一个必传参数prepay_id只有两小时的有效期，所以在重入时间超过2小时的时候需要重新请求下单接口获取新的prepay_id
     * - 其他详见：[时间规则](https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=4_2)
     * - 建议：最短失效时间间隔大于1分钟
     *
     * @param string $timeExpire
     *
     * @return static
     */
    public function setTimeExpire($timeExpire)
    {
        $this->_values['time_expire'] = $timeExpire;
        return $this;
    }

    /**
     * 设置订单优惠标记
     *
     * - 订单优惠标记，代金券或立减优惠功能的参数
     * - 说明详见：[代金券或立减优惠](https://pay.weixin.qq.com/wiki/doc/api/tools/sp_coupon.php?chapter=12_1)
     *
     * @param string $goodsTag
     *
     * @return static
     */
    public function setGoodsTag($goodsTag)
    {
        $this->_values['goods_tag'] = $goodsTag;
        return $this;
    }

    /**
     * 设置通知地址
     *
     * - 接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数
     *
     * @param string $notifyUrl
     *
     * @return static
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->_values['notify_url'] = $notifyUrl;
        return $this;
    }

    /**
     * 设置指定支付方式
     *
     * - no_credit--指定不能使用信用卡支付
     *
     * @param string $limitPay
     *
     * @return static
     */
    public function setLimitPay($limitPay)
    {
        $this->_values['limit_pay'] = $limitPay;
        return $this;
    }

    /**
     * 设置开发票入口开放标识
     *
     * - Y，传入Y时，支付成功消息和支付详情页将出现开票入口
     * - 需要在微信支付商户平台或微信公众平台开通电子发票功能，传此字段才可生效
     *
     * @param string $receipt
     *
     * @return static
     */
    public function setReceipt($receipt)
    {
        $this->_values['receipt'] = $receipt;
        return $this;
    }

    /**
     * 设置交易类型
     *
     * - 不同trade_type决定了调起支付的方式，请根据支付产品正确上传
     *      1. JSAPI--JSAPI支付（或小程序支付）
     *      2. NATIVE--Native支付
     *      3. APP--app支付
     *      4. MWEB--H5支付
     *      5. MICROPAY--付款码支付，付款码支付有单独的支付接口，所以接口不需要上传，该字段在对账单中会出现
     *
     * @param string $tradeType
     *
     * @return static
     */
    public function setTradeType($tradeType)
    {
        $this->_values['trade_type'] = $tradeType;
        return $this;
    }

    /**
     * 商品ID
     *
     * - trade_type=NATIVE时，此参数必传。此参数为二维码中包含的商品ID，商户自行定义
     *
     * @param string $productId
     *
     * @return static
     */
    public function setProductId($productId)
    {
        $this->_values['product_id'] = $productId;
        return $this;
    }

    /**
     * 用户标识
     *
     * - trade_type=JSAPI时（即JSAPI支付），此参数必传，此参数为微信用户在商户对应appid下的唯一标识
     * - openid如何获取，可参考【[获取openid](https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=4_4)】
     * - 企业号请使用【[企业号OAuth2.0接口](https://qydev.weixin.qq.com/wiki/index.php?title=OAuth%E9%AA%8C%E8%AF%81%E6%8E%A5%E5%8F%A3)】获取企业号内成员userid，再调用【[企业号userid转openid接口](https://qydev.weixin.qq.com/wiki/index.php?title=Userid%E4%B8%8Eopenid%E4%BA%92%E6%8D%A2%E6%8E%A5%E5%8F%A3)】进行转换
     *
     * @param string $openId
     *
     * @return static
     */
    public function setOpenId($openId)
    {
        $this->_values['openid'] = $openId;
        return $this;
    }

    /**
     * 场景信息
     *
     * - 该字段常用于线下活动时的场景信息上报，支持上报实际门店信息，商户也可以按需求自己上报相关信息
     * - 涉及字段：
     *      1. id：门店编号，由商户自定义
     *      2. name：门店名称 ，由商户自定义
     *      3. area_code：门店所在地行政区划码，详细见《[最新县及县以上行政区划代码](https://pay.weixin.qq.com/wiki/doc/api/download/store_adress.csv)》
     *      4. address：门店详细地址 ，由商户自定义
     *
     * @param array $sceneInfo
     *
     * @return static
     */
    public function setSceneInfo($sceneInfo)
    {
        $this->_values['scene_info'] = Json::encode($sceneInfo);
        return $this;
    }

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
        return 'SUCCESS' === I::get($this->_result, 'return_code') && 'SUCCESS' === I::get($this->_values, 'result_code');
    }

    /**
     * 返回用于拉起微信支付用的前端参数
     *
     * @return array
     */
    public function getCallArray()
    {
        if (false === $this->isSuccess()) {
            throw new Exception(I::get($this->_result, 'return_msg'));
        }
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
        return $array;
    }

    /**
     * 支付结果通知以及退款结果通知
     *
     * - 如果校验成功，会输出成功给微信，并返回数据
     * - 如果校验失败，返回 false
     *
     * @return mixed
     */
    public function notify()
    {
        $xml = (new Request())->getRawBody();
        $array = Xml::toArray($xml);
        $code = I::get($array, 'return_code');
        if ('SUCCESS' !== $code) {
            throw new Exception(I::get($array, 'return_msg'));
        }
        $sign = $array['sign'];
        unset($array['sign']);
        if ($this->getSign($array) == $sign) {
            echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
            return $array;
        }
        return false;
    }

    /**
     * 设置微信订单号
     *
     * - 微信的订单号，优先使用
     *
     * @param string $transactionId
     *
     * @return static
     */
    public function setTransactionId($transactionId)
    {
        $this->_values['transaction_id'] = $transactionId;
        return $this;
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
     * 设置商户退款单号
     *
     * - 商户系统内部的退款单号，商户系统内部唯一，只能是数字、大小写字母_-|*@ ，同一退款单号多次请求只退一笔
     * - 如未设置，则和【商户订单号】一致
     *
     * @param string $outRefundNo
     *
     * @return static
     */
    public function setOutRefundNo($outRefundNo = null)
    {
        $this->_values['out_refund_no'] = null === $outRefundNo ? I::get($this->_values, 'out_trade_no') : $outRefundNo;
        return $this;
    }

    /**
     * 设置退款金额
     *
     * - 退款总金额，订单总金额，单位为分，只能为整数
     * - 详见[支付金额](https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=4_2)
     * - 如未设置，则和【订单金额】一致
     *
     * @param integer $refundFee
     *
     * @return static
     */
    public function setRefundFee($refundFee = null)
    {
        $this->_values['refund_fee'] = null === $refundFee ? I::get($this->_values, 'total_fee') : $refundFee;
        return $this;
    }

    /**
     * 设置退款货币种类
     *
     * - 退款货币类型，需与支付一致，或者不填
     * - 符合ISO 4217标准的三位字母代码，默认人民币：CNY
     * - 其他值列表详见[货币类型](https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=4_2)
     *
     * @param string $refundFeeType
     *
     * @return static
     */
    public function setRefundFeeType($refundFeeType = 'CNY')
    {
        $this->_values['refund_fee_type'] = $refundFeeType;
        return $this;
    }

    /**
     * 设置退款原因
     *
     * - 若商户传入，会在下发给用户的退款消息中体现退款原因
     * - 注意：若订单退款金额≤1元，且属于部分退款，则不会在退款消息中体现退款原因
     *
     * @param string $refundDesc
     *
     * @return static
     */
    public function setRefundDesc($refundDesc)
    {
        $this->_values['refund_desc'] = $refundDesc;
        return $this;
    }

    /**
     * 设置退款资金来源
     *
     * - 仅针对老资金流商户使用：
     *      1. REFUND_SOURCE_UNSETTLED_FUNDS---未结算资金退款（默认使用未结算资金退款）
     *      2. REFUND_SOURCE_RECHARGE_FUNDS---可用余额退款
     *
     * @param string $refundAccount
     *
     * @return static
     */
    public function setRefundAccount($refundAccount)
    {
        $this->_values['refund_account'] = $refundAccount;
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
     * 设置偏移量
     *
     * - 查询退款：偏移量，当部分退款次数超过10次时可使用，表示返回的查询结果从这个偏移量开始取记录
     *
     * @param integer $offset
     *
     * @return static
     */
    public function setOffset($offset)
    {
        $this->_values['offset'] = $offset;
        return $this;
    }

    /**
     * 设置微信退款单号
     *
     * - 微信生成的退款单号，在申请退款接口有返回
     *
     * @param string $refundId
     *
     * @return static
     */
    public function setRefundId($refundId)
    {
        $this->_values['refund_id'] = $refundId;
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
     * 设置对账单日期
     *
     * - 下载对账单的日期，格式：20140603
     *
     * @param string $billDate
     *
     * @return static
     */
    public function setBillDate($billDate)
    {
        $this->_values['bill_date'] = $billDate;
        return $this;
    }

    /**
     * 设置账单类型
     *
     * - ALL（默认值），返回当日所有订单信息（不含充值退款订单）
     * - SUCCESS，返回当日成功支付的订单（不含充值退款订单）
     * - REFUND，返回当日退款订单（不含充值退款订单）
     * - RECHARGE_REFUND，返回当日充值退款订单
     *
     * @param string $billType
     *
     * @return static
     */
    public function setBillType($billType = 'ALL')
    {
        $this->_values['bill_type'] = $billType;
        return $this;
    }

    /**
     * 压缩账单
     *
     * - 非必传参数，固定值：GZIP，返回格式为.gzip的压缩包账单。不传则默认为数据流形式
     *
     * @param string $tarType
     *
     * @return static
     */
    public function setTarType($tarType = 'GZIP')
    {
        $this->_values['tar_type'] = $tarType;
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
     * 设置资金账户类型
     *
     * - 账单的资金来源账户：
     *      1. Basic  基本账户
     *      2. Operation 运营账户
     *      3. Fees 手续费账户
     *
     * @param string $accountType
     *
     * @return static
     */
    public function setAccountType($accountType)
    {
        $this->_values['account_type'] = $accountType;
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
     * 设置URL链接
     *
     * - 此参数不需要 URLencode，内部会做处理
     *
     * @param string $longUrl
     *
     * @return static
     */
    public function setLongUrl($longUrl)
    {
        $this->_values['long_url'] = $longUrl;
        return $this;
    }

    /**
     * 转换短链接
     *
     * @return static
     */
    public function shortUrl()
    {
        if (null === ($longUrl = I::get($this->_values, 'long_url'))) {
            throw new Exception('缺少 long_url 参数！');
        }
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
    public function getLongUrlValue()
    {
        if (null === ($productId = I::get($this->_values, 'product_id'))) {
            throw new Exception('缺少 product_id 参数！');
        }
        $values = [
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'time_stamp' => time(),
            'nonce_str' => Strings::random(),
            'product_id' => $productId,
        ];
        $values['sign'] = $this->getSign($values);
        return 'weixin：//wxpay/bizpayurl?sign=' . $values['sign'] .
            '&appid=' . $values['appid'] .
            '&mch_id=' . $values['mch_id'] .
            '&product_id=' . $values['product_id'] .
            '&time_stamp=' . $values['time_stamp'] .
            '&nonce_str=' . $values['nonce_str'];
    }

    /**
     * 设置开始时间
     *
     * - 按用户评论时间批量拉取的起始时间，格式为yyyyMMddHHmmss
     *
     * @param string $beginTime
     *
     * @return static
     */
    public function setBeginTime($beginTime)
    {
        $this->_values['begin_time'] = $beginTime;
        return $this;
    }

    /**
     * 设置结束时间
     *
     * - 按用户评论时间批量拉取的结束时间，格式为yyyyMMddHHmmss
     *
     * @param string $endTime
     *
     * @return static
     */
    public function setEndTime($endTime)
    {
        $this->_values['end_time'] = $endTime;
        return $this;
    }

    /**
     * 设置条数
     *
     * - 一次拉取的条数, 最大值是200，默认是200
     *
     * @param integer $limit
     *
     * @return static
     */
    public function setLimit($limit = 200)
    {
        $this->_values['limit'] = $limit;
        return $this;
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
