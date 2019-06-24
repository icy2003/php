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
     * 设备号
     *
     * @var string
     */
    protected $_deviceInfo = 'WEB';

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
        $this->_deviceInfo = $deviceInfo;
    }

    /**
     * 签名类型
     *
     * @var string
     */
    protected $_signType = 'MD5';

    /**
     * 设置签名类型
     *
     * - 目前支持HMAC-SHA256和MD5，默认为MD5
     *
     * @param string $signType
     *
     * @return void
     */
    public function setSignType($signType)
    {
        $this->_signType = $signType;
    }

    /**
     * 商品描述
     *
     * @var string
     */
    protected $_body;

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
        $this->_body = $body;
    }

    /**
     * 商品详情
     *
     * @var string
     */
    protected $_detail;

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
        $this->_detail = $detail;
    }

    /**
     * 附加数据
     *
     * @var string
     */
    protected $_attach;

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
        $this->_attach = $attach;
    }

    /**
     * 商户订单号
     *
     * @var string
     */
    protected $_outTradeNo;

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
        $this->_outTradeNo = $outTradeNo;
    }

    /**
     * 货币类型
     *
     * @var string
     */
    protected $_feeType = 'CNY';

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
    public function setFeeType($feeType)
    {
        $this->_feeType = $feeType;
    }

    /**
     * 总金额
     *
     * @var integer
     */
    protected $_totalFee;

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
        $this->_totalFee = $totalFee;
    }

    /**
     * 交易起始时间
     *
     * @var string
     */
    protected $_timeStart;

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
        $this->_timeStart = $timeStart;
    }

    /**
     * 交易结束时间
     *
     * @var string
     */
    protected $_timeExpire;

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
        $this->_timeExpire = $timeExpire;
    }

    /**
     * 订单优惠标记
     *
     * @var string
     */
    protected $_goodsTag;

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
        $this->_goodsTag = $goodsTag;
    }

    /**
     * 通知地址
     *
     * @var string
     */
    protected $_notifyUrl;

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
        $this->_notifyUrl = $notifyUrl;
    }

    /**
     * 指定支付方式
     *
     * @var string
     */
    protected $_limitPay;

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
        $this->_limitPay = $limitPay;
    }

    /**
     * 开发票入口开放标识
     *
     * @var string
     */
    protected $_receipt;

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
        $this->_receipt = $receipt;
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
        if (null === $this->_body) {
            throw new Exception("缺少统一支付接口必填参数：body");
        }
        if (null === $this->_outTradeNo) {
            throw new Exception("缺少统一支付接口必填参数：out_trade_no");
        }
        if (null === $this->_totalFee) {
            throw new Exception("缺少统一支付接口必填参数：total_fee");
        }
        if (null === $this->_notifyUrl) {
            throw new Exception("缺少统一支付接口必填参数：notify_url");
        }
        $values = array_filter([
            'appid' => $this->_appId,
            'mch_id' => $this->_mchId,
            'device_info' => $this->_deviceInfo,
            'nonce_str' => Strings::random(),
            'sign_type' => $this->_signType,
            'body' => $this->_body,
            'detail' => $this->_detail,
            'attach' => $this->_attach,
            'out_trade_no' => $this->_outTradeNo,
            'fee_type' => $this->_feeType,
            'total_fee' => $this->_totalFee,
            'spbill_create_ip' => $this->getIp(),
            'time_start' => $this->_timeStart,
            'time_expire' => $this->_timeExpire,
            'goods_tag' => $this->_goodsTag,
            'notify_url' => $this->_notifyUrl,
            'trade_type' => 'APP',
            'limit_pay' => $this->_limitPay,
            'receipt' => $this->_receipt,
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
     * 支付结果通知校验数据
     *
     * - 如果校验成功，会输出成功给微信，并返回数据
     * - 如果校验失败，返回 false
     *
     * @return boolean
     */
    public function payNotify()
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
}
