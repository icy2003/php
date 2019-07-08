<?php
/**
 * Trait PaySetterTrait
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis\wechat;

use icy2003\php\I;
use icy2003\php\ihelpers\Json;

/**
 * Pay setter
 */
trait PaySetterTrait
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
     * @return static
     */
    public function setCertPath($certPath)
    {
        $this->_certPath = $certPath;
        return $this;
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
     * @return static
     */
    public function setCertKeyPath($certKeyPath)
    {
        $this->_certKeyPath = $certKeyPath;
        return $this;
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
     * 设置交易类型
     *
     * - 不同 trade_type 决定了调起支付的方式，请根据支付产品正确上传
     *      1. JSAPI--JSAPI 支付（或小程序支付）
     *      2. NATIVE--Native 支付
     *      3. APP--app 支付
     *      4. MWEB--H5 支付
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
     * - NATIVE（扫码支付）涉及字段：
     *      - store_info：
     *          - id：门店编号，由商户自定义
     *          - name：门店名称 ，由商户自定义
     *          - area_code：门店所在地行政区划码，详细见《[最新县及县以上行政区划代码](https://pay.weixin.qq.com/wiki/doc/api/download/store_adress.csv)》
     *          - address：门店详细地址 ，由商户自定义
     * - MWEB（H5支付）涉及字段：
     *      - h5_info：
     *          - type：场景类型，如：IOS、Android、Wap
     *          - app_name：应用名
     *          - package_name：安卓填，包名
     *          - bundle_id：IOS填，bundle_id
     *          - wap_name：WAP网站填，WAP 网站名
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
     * 设置预支付ID
     *
     * - 调用统一下单接口生成的预支付ID
     *
     * @param string $prepayId
     *
     * @return static
     */
    public function setPrepayId($prepayId)
    {
        $this->_values['prepay_id'] = $prepayId;
        return $this;
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
}
