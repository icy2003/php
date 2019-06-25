<?php
/**
 * Class App
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis\wechat\pay;

use Exception;
use icy2003\php\I;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Request;
use icy2003\php\ihelpers\Strings;
use icy2003\php\ihelpers\Xml;

/**
 * App 支付
 *
 * - @see https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=8_1
 */
class App
{

    use UtilTrait;

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
     * 数据包
     *
     * @var array
     */
    protected $_values = [];

    /**
     * 设置设备号
     *
     * - 终端设备号(门店号或收银设备ID)，默认请传"WEB"
     *
     * @param string $deviceInfo
     *
     * @return void
     */
    public function setDeviceInfo($deviceInfo)
    {
        $this->_values['device_info'] = $deviceInfo;
    }

    /**
     * 设置签名类型
     *
     * - 目前支持HMAC-SHA256和MD5，默认为MD5
     *
     * @param string $signType
     *
     * @return void
     */
    public function setSignType($signType = 'MD5')
    {
        $this->_values['sign_type'] = $signType;
    }

    /**
     * 设置商品描述
     *
     * - 商品描述交易字段格式根据不同的应用场景按照以下格式：APP——需传入应用市场上的APP名字-实际商品名称，天天爱消除-游戏充值。
     *
     * @param string $body
     *
     * @return void
     */
    public function setBody($body)
    {
        $this->_values['body'] = $body;
    }

    /**
     * 设置商品详情
     *
     * - 商品详细描述，对于使用单品优惠的商户，该字段必须按照规范上传
     * - 详见：[单品优惠参数说明](https://pay.weixin.qq.com/wiki/doc/api/danpin.php?chapter=9_102&index=2)
     *
     * @param string $detail
     *
     * @return void
     */
    public function setDetail($detail)
    {
        $this->_values['detail'] = $detail;
    }

    /**
     * 设置附加数据
     *
     * - 附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
     *
     * @param string $attach
     *
     * @return void
     */
    public function setAttach($attach)
    {
        $this->_values['attach'] = $attach;
    }

    /**
     * 设置商户订单号
     *
     * - 商户系统内部订单号，要求32个字符内，只能是数字、大小写字母_-|*且在同一个商户号下唯一
     * - 详见：[商户订单号](https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=4_2)
     *
     * @param string $outTradeNo
     *
     * @return void
     */
    public function setOutTradeNo($outTradeNo)
    {
        $this->_values['out_trade_no'] = $outTradeNo;
    }

    /**
     * 设置货币类型
     *
     * - 符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表
     * - 详见：[货币类型](https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=4_2)
     *
     * @param string $feeType
     *
     * @return void
     */
    public function setFeeType($feeType = 'CNY')
    {
        $this->_values['fee_type'] = $feeType;
    }

    /**
     * 设置总金额
     *
     * - 订单总金额，单位为分
     * - 详见：[支付金额](https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=4_2)
     *
     * @param integer $totalFee
     *
     * @return void
     */
    public function setTotalFee($totalFee)
    {
        $this->_values['total_fee'] = $totalFee;
    }

    /**
     * 设置交易起始时间
     *
     * - 订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010
     * - 其他详见：[时间规则](https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=4_2)
     *
     * @param string $timeStart
     *
     * @return void
     */
    public function setTimeStart($timeStart)
    {
        $this->_values['time_start'] = $timeStart;
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
     * @return void
     */
    public function setTimeExpire($timeExpire)
    {
        $this->_values['time_expire'] = $timeExpire;
    }

    /**
     * 设置订单优惠标记
     *
     * - 订单优惠标记，代金券或立减优惠功能的参数
     * - 说明详见：[代金券或立减优惠](https://pay.weixin.qq.com/wiki/doc/api/tools/sp_coupon.php?chapter=12_1)
     *
     * @param string $goodsTag
     *
     * @return void
     */
    public function setGoodsTag($goodsTag)
    {
        $this->_values['goods_tag'] = $goodsTag;
    }

    /**
     * 设置通知地址
     *
     * - 接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数
     *
     * @param string $notifyUrl
     *
     * @return void
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->_values['notify_url'] = $notifyUrl;
    }

    /**
     * 设置指定支付方式
     *
     * - no_credit--指定不能使用信用卡支付
     *
     * @param string $limitPay
     *
     * @return void
     */
    public function setLimitPay($limitPay)
    {
        $this->_values['limit_pay'] = $limitPay;
    }

    /**
     * 设置开发票入口开放标识
     *
     * - Y，传入Y时，支付成功消息和支付详情页将出现开票入口
     * - 需要在微信支付商户平台或微信公众平台开通电子发票功能，传此字段才可生效
     *
     * @param string $receipt
     *
     * @return void
     */
    public function setReceipt($receipt)
    {
        $this->_values['receipt'] = $receipt;
    }

    /**
     * 统一下单
     *
     * - 商户系统先调用该接口在微信支付服务后台生成预支付交易单，返回正确的预支付交易会话标识后再在APP里面调起支付
     *
     * @return array
     */
    public function pay()
    {
        if (null === ($body = I::get($this->_values, 'body'))) {
            throw new Exception("缺少统一支付接口必填参数：body");
        }
        if (null === ($outTradeNo = I::get($this->_values, 'out_trade_no'))) {
            throw new Exception("缺少统一支付接口必填参数：out_trade_no");
        }
        if (null === ($totalFee = I::get($this->_values, 'total_fee'))) {
            throw new Exception("缺少统一支付接口必填参数：total_fee");
        }
        if (null === ($notifyUrl = I::get($this->_values, 'notify_url'))) {
            throw new Exception("缺少统一支付接口必填参数：notify_url");
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
            'trade_type' => 'APP',
            'limit_pay' => I::get($this->_values, 'limit_pay'),
            'receipt' => I::get($this->_values, 'receipt'),
        ]);
        $values['sign'] = $this->getSign($values);
        $responseXml = Http::body('https://api.mch.weixin.qq.com/pay/unifiedorder', Xml::fromArray($values));
        $array = Xml::toArray($responseXml);
        $code = I::get($array, 'return_code');
        if ('SUCCESS' !== $code) {
            throw new Exception(I::get($array, 'return_msg'));
        }
        $return = [
            'appid' => $this->_appId,
            'partnerid' => $this->_mchId,
            'prepayid' => I::get($array, 'prepay_id'),
            'package' => 'Sign=WXPay',
            'noncestr' => Strings::random(),
            'timestamp' => time(),
        ];
        $return['sign'] = $this->getSign($return);
        return $return;
    }

    /**
     * 支付结果通知以及退款结果通知
     *
     * - 如果校验成功，会输出成功给微信，并返回数据
     * - 如果校验失败，返回 false
     *
     * @return boolean
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
     * @return void
     */
    public function setTransactionId($transactionId)
    {
        $this->_values['transaction_id'] = $transactionId;
    }

    /**
     * 该接口提供所有微信支付订单的查询，商户可以通过该接口主动查询订单状态
     *
     * @return array
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
            throw new Exception("transaction_id 和 out_trade_no 必须二选一");
        }
        $values['sign'] = $this->getSign($values);
        $responseBody = Http::body('https://api.mch.weixin.qq.com/pay/orderquery', Xml::fromArray($values));
        return Xml::toArray($responseBody);
    }

    /**
     * 关闭订单
     *
     * @return array
     */
    public function closeOrder()
    {
        if (null === ($outTradeNo = I::get($this->_values, 'out_trade_no'))) {
            throw new Exception("缺少必填参数：out_trade_no");
        }
        $values = [
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'out_trade_no' => $outTradeNo,
            'nonce_str' => Strings::random(),
        ];
        $values['sign'] = $this->getSign($values);
        $responseBody = Http::body('https://api.mch.weixin.qq.com/pay/closeorder', Xml::fromArray($values));
        return Xml::toArray($responseBody);
    }

    /**
     * 设置商户退款单号
     *
     * - 商户系统内部的退款单号，商户系统内部唯一，只能是数字、大小写字母_-|*@ ，同一退款单号多次请求只退一笔
     * - 如未设置，则和【商户订单号】一致
     *
     * @param string $outRefundNo
     *
     * @return void
     */
    public function setOutRefundNo($outRefundNo = null)
    {
        $this->_values['out_refund_no'] = null === $outRefundNo ? I::get($this->_values, 'out_trade_no') : $outRefundNo;
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
     * @return void
     */
    public function setRefundFee($refundFee = null)
    {
        $this->_values['refund_fee'] = null === $refundFee ? I::get($this->_values, 'total_fee') : $refundFee;
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
     * @return void
     */
    public function setRefundFeeType($refundFeeType = 'CNY')
    {
        $this->_values['refund_fee_type'] = $refundFeeType;
    }

    /**
     * 设置退款原因
     *
     * - 若商户传入，会在下发给用户的退款消息中体现退款原因
     * - 注意：若订单退款金额≤1元，且属于部分退款，则不会在退款消息中体现退款原因
     *
     * @param string $refundDesc
     *
     * @return void
     */
    public function setRefundDesc($refundDesc)
    {
        $this->_values['refund_desc'] = $refundDesc;
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
     * @return void
     */
    public function setRefundAccount($refundAccount)
    {
        $this->_values['refund_account'] = $refundAccount;
    }

    /**
     * 申请退款
     *
     * @return array
     */
    public function refund()
    {
        if (null === $this->_certPath) {
            throw new Exception("请使用 setCertPath 提供证书路径");
        }
        if (null === $this->_certKeyPath) {
            throw new Exception("请使用 setCertKeyPath 提供证书密钥路径");
        }
        $this->setOutRefundNo();
        $this->setRefundFee();
        $values = array_filter([
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'nonce_str' => Strings::random(),
            'sign_type' => I::get($this->_values, 'sign_type'),
            'out_refund_no' => I::get($this->_values, 'out_refund_no'),
            'total_fee' => I::get($this->_values, 'total_fee'),
            'refund_fee' => I::get($this->_values, 'refund_fee'),
            'refund_fee_type' => I::get($this->_values, 'refund_fee_type'),
            'refund_desc' => I::get($this->_values, 'refund_desc'),
            'refund_account' => I::get($this->_values, 'refund_account'),
            'notify_url' => I::get($this->_values, 'notify_url'),
            'transaction_id' => I::get($this->_values, 'transaction_id'),
            'out_trade_no' => I::get($this->_values, 'out_trade_no'),
        ]);
        if (false === Arrays::keyExistsSome(['transaction_id', 'out_trade_no'], $values)) {
            throw new Exception("transaction_id 和 out_trade_no 必须二选一");
        }
        $values['sign'] = $this->getSign($values);
        $responseBody = Http::body('https://api.mch.weixin.qq.com/secapi/pay/refund', Xml::fromArray($values), [], [
            'cert' => $this->_certPath,
            'ssl_key' => $this->_certKeyPath,
        ]);
        return Xml::toArray($responseBody);
    }

    /**
     * 设置偏移量
     *
     * - 查询退款：偏移量，当部分退款次数超过10次时可使用，表示返回的查询结果从这个偏移量开始取记录
     *
     * @param integer $offset
     *
     * @return void
     */
    public function setOffset($offset)
    {
        $this->_values['offset'] = $offset;
    }

    /**
     * 设置微信退款单号
     *
     * - 微信生成的退款单号，在申请退款接口有返回
     *
     * @param string $refundId
     *
     * @return void
     */
    public function setRefundId($refundId)
    {
        $this->_values['refund_id'] = $refundId;
    }

    /**
     * 查询退款
     *
     * @return array
     */
    public function refundQuery()
    {
        $values = array_filter([
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'nonce_str' => Strings::random(),
            'offset' => I::get($this->_values, 'offset'),
            'transaction_id' => I::get($this->_values, 'transaction_id'),
            'out_trade_no' => I::get($this->_values, 'out_trade_no'),
            'out_refund_no' => I::get($this->_values, 'out_refund_no'),
            'refund_id' => I::get($this->_values, 'refund_id'),
        ]);
        if (false === Arrays::keyExistsSome(['transaction_id', 'out_trade_no', 'out_refund_no', 'refund_id'], $values)) {
            throw new Exception("transaction_id、out_trade_no、out_refund_no 和 refund_id 必须四选一");
        }
        $values['sign'] = $this->getSign($values);
        $responseBody = Http::body('https://api.mch.weixin.qq.com/pay/refundquery', Xml::fromArray($values));
        return Xml::toArray($responseBody);
    }

    /**
     * 设置对账单日期
     *
     * - 下载对账单的日期，格式：20140603
     *
     * @param string $billDate
     *
     * @return void
     */
    public function setBillDate($billDate)
    {
        $this->_values['bill_date'] = $billDate;
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
     * @return void
     */
    public function setBillType($billType = 'ALL')
    {
        $this->_values['bill_type'] = $billType;
    }

    /**
     * 压缩账单
     *
     * - 非必传参数，固定值：GZIP，返回格式为.gzip的压缩包账单。不传则默认为数据流形式
     *
     * @param string $tarType
     *
     * @return void
     */
    public function setTarType($tarType = 'GZIP')
    {
        $this->_values['tar_type'] = $tarType;
    }

    /**
     * 下载对账单
     *
     * @return array
     */
    public function downloadBill()
    {
        if (null === ($billDate = I::get($this->_values, 'bill_date'))) {
            throw new Exception("缺少 bill_date 参数！");
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
        return Xml::toArray($responseBody);
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
     * @return void
     */
    public function setAccountType($accountType)
    {
        $this->_values['account_type'] = $accountType;
    }

    /**
     * 下载资金账单
     *
     * @return array
     */
    public function downloadFundFlow()
    {
        if (null === ($accoutType = I::get($this->_values, 'account_type'))) {
            throw new Exception("缺少 account_type 参数！");
        }
        if (null === $this->_certPath) {
            throw new Exception("请使用 setCertPath 提供证书路径");
        }
        if (null === $this->_certKeyPath) {
            throw new Exception("请使用 setCertKeyPath 提供证书密钥路径");
        }
        $values = array_filter([
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'nonce_str' => Strings::random(),
            'sign_type' => 'HMAC-SHA256',
            'bill_date' => I::get($this->_values, 'bill_date'),
            'account_type' => $accoutType,
            'tar_type' => I::get($this->_values, 'tar_type'),
        ]);
        $values['sign'] = $this->getSign($values);
        $responseBody = Http::body('https://api.mch.weixin.qq.com/pay/downloadfundflow', Xml::fromArray($values), [], [
            'cert' => $this->_certPath,
            'ssl_key' => $this->_certKeyPath,
        ]);
        return Xml::toArray($responseBody);
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
     * 设置开始时间
     *
     * - 按用户评论时间批量拉取的起始时间，格式为yyyyMMddHHmmss
     *
     * @param string $beginTime
     *
     * @return void
     */
    public function setBeginTime($beginTime)
    {
        $this->_values['begin_time'] = $beginTime;
    }

    /**
     * 设置结束时间
     *
     * - 按用户评论时间批量拉取的结束时间，格式为yyyyMMddHHmmss
     *
     * @param string $endTime
     *
     * @return void
     */
    public function setEndTime($endTime)
    {
        $this->_values['end_time'] = $endTime;
    }

    /**
     * 设置条数
     *
     * - 一次拉取的条数, 最大值是200，默认是200
     *
     * @param integer $limit
     *
     * @return void
     */
    public function setLimit($limit = 200)
    {
        $this->_values['limit'] = $limit;
    }

    /**
     * 拉取订单评价数据
     *
     * @todo 此接口有问题，但我也不知道为什么
     *
     * @return array
     */
    public function batchQueryComment()
    {
        if (null === ($beginTime = I::get($this->_values, 'begin_time'))) {
            throw new Exception("缺少 begin_time 参数！");
        }
        if (null === ($endTime = I::get($this->_values, 'end_time'))) {
            throw new Exception("缺少 end_time 参数！");
        }
        if (null === ($offset = I::get($this->_values, 'offset'))) {
            throw new Exception("缺少 offset 参数！");
        }
        if (null === $this->_certPath) {
            throw new Exception("请使用 setCertPath 提供证书路径");
        }
        if (null === $this->_certKeyPath) {
            throw new Exception("请使用 setCertKeyPath 提供证书密钥路径");
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
        return Xml::toArray($responseBody);
    }
}
