<?php
/**
 * Trait PaySetterTrait
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis\alipay;

use icy2003\php\I;

/**
 * Pay setter
 */
trait PaySetterTrait
{

    /**
     * 支付宝分配给开发者的应用ID
     *
     * @var string
     */
    protected $_appId;

    protected $_rsaPrivateKey;

    /**
     * 发送的数据
     *
     * @var array
     */
    protected $_values = [
        'biz_content' => [],
    ];

    /**
     * 接收的结果
     *
     * - 每执行一个接口都会修改此值
     *
     * @var array
     */
    protected $_result = [];

    /**
     * 仅支持JSON
     *
     * @param string $format
     *
     * @return static
     */
    public function setFormat($format)
    {
        $this->_values['format'] = $format;
        return $this;
    }

    /**
     * HTTP/HTTPS开头字符串
     *
     * @param string $returnUrl
     *
     * @return static
     */
    public function setReturnUrl($returnUrl)
    {
        $this->_values['return_url'] = $returnUrl;
        return $this;
    }

    /**
     * 请求使用的编码格式，如utf-8,gbk,gb2312等
     *
     * @param string $charset
     *
     * @return static
     */
    public function setCharset($charset)
    {
        $this->_values['charset'] = $charset;
        return $this;
    }

    /**
     * 商户生成签名字符串所使用的签名算法类型
     *
     * - 目前支持RSA2和RSA
     * - 推荐使用RSA2
     *
     * @param string $signType
     *
     * @return static
     */
    public function setSignType($signType)
    {
        $this->_values['sign_type'] = $signType;
        return $this;
    }

    /**
     * 发送请求的时间，格式"yyyy-MM-dd HH:mm:ss"
     *
     * @param string $timestamp
     *
     * @return static
     */
    public function setTimestamp($timestamp)
    {
        $this->_values['timestamp'] = $timestamp;
        return $this;
    }

    /**
     * 支付宝服务器主动通知商户服务器里指定的页面http/https路径
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
     * 第三方应用授权 TOKEN
     *
     * - 详见[应用授权概述](https://docs.open.alipay.com/20160728150111277227/intro)
     *
     * @param string $appAuthToken
     *
     * @return static
     */
    public function setAppAuthToken($appAuthToken){
        $this->_values['app_auth_token'] = $appAuthToken;
        return $this;
    }

    /**
     * 请求参数的集合，最大长度不限，除公共参数外所有请求参数都必须放在这个参数中传递，具体参照各产品快速接入文档
     *
     * @param string $key
     * @param string $value
     *
     * @return static
     */
    public function setBizContent($key, $value)
    {
        $this->_values['biz_content'][$key] = $value;
        return $this;
    }

    /**
     * 该笔订单允许的最晚付款时间，逾期将关闭交易
     *
     * - 取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）
     * - 该参数数值不接受小数点， 如 1.5h，可转换为 90m
     *
     * @param string $timeoutExpress
     *
     * @return static
     */
    public function setBizContentTimeoutExpress($timeoutExpress)
    {
        return $this->setBizContent('timeout_express', $timeoutExpress);
    }

    /**
     * 订单总金额
     *
     * - 单位为元，精确到小数点后两位，取值范围[0.01,100000000]
     *
     * @param double $totalAmount
     *
     * @return static
     */
    public function setBizContentTotalAmount($totalAmount)
    {
        return $this->setBizContent('total_amount', $totalAmount);
    }

    /**
     * 销售产品码，商家和支付宝签约的产品码
     *
     * @param string $productCode
     *
     * @return static
     */
    public function setBizContentProductCode($productCode)
    {
        return $this->setBizContent('product_code', $productCode);
    }

    /**
     * 对一笔交易的具体描述信息
     *
     * - 如果是多种商品，请将商品描述字符串累加传给body
     *
     * @param string $body
     *
     * @return static
     */
    public function setBizContentBody($body)
    {
        return $this->setBizContent('body', $body);
    }

    /**
     * 商品的标题/交易标题/订单标题/订单关键字等
     *
     * @param string $subject
     *
     * @return static
     */
    public function setBizContentSubject($subject)
    {
        return $this->setBizContent('subject', $subject);
    }

    /**
     * 商户网站唯一订单号
     *
     * @param string $outTradeNo
     *
     * @return static
     */
    public function setBizContentOutTradeNo($outTradeNo)
    {
        return $this->setBizContent('out_trade_no', $outTradeNo);
    }

    /**
     * 绝对超时时间，格式为yyyy-MM-dd HH:mm
     *
     * @param string $timeExpire
     *
     * @return static
     */
    public function setBizContentTimeExpire($timeExpire)
    {
        return $this->setBizContent('time_expire', $timeExpire);
    }

    /**
     * 商品主类型
     *
     * - 0：虚拟类商品
     * - 1：实物类商品
     *
     * @param string $goodsType
     *
     * @return static
     */
    public function setBizContentGoodsType($goodsType)
    {
        return $this->setBizContent('goods_type', $goodsType);
    }

    /**
     * 优惠参数
     *
     * - 仅与支付宝协商后可用
     *
     * @param string $promoParams
     *
     * @return static
     */
    public function setBizContentPromoParams($promoParams)
    {
        return $this->setBizContent('promo_params', $promoParams);
    }

    /**
     * 公用回传参数
     *
     * - 如果请求时传递了该参数，则返回给商户时会回传该参数
     * - 支付宝只会在同步返回（包括跳转回商户网站）和异步通知时将该参数原样返回
     * - 本参数必须进行UrlEncode之后才可以发送给支付宝
     *
     * @param string $passbackParams
     *
     * @return static
     */
    public function setBizContentPassbackParams($passbackParams)
    {
        return $this->setBizContent('passback_params', $passbackParams);
    }

    /**
     * 业务扩展参数
     *
     * @param string $key
     * @param string $value
     *
     * @return static
     */
    public function setBizContentExtendParams($key, $value)
    {
        $array = I::get($this->_values, 'biz_content.extend_params', []);
        $array[$key] = $value;
        return $this->setBizContent('extend_params', $array);
    }

    /**
     * 系统商编号
     *
     * - 该参数作为系统商返佣数据提取的依据，请填写系统商签约协议的PID
     *
     * @param string $extendParamsSysServiceProviderId
     *
     * @return static
     */
    public function setBizContentExtendParamsSysServiceProviderId($extendParamsSysServiceProviderId)
    {
        return $this->setBizContentExtendParams('sys_service_provider_id', $extendParamsSysServiceProviderId);
    }

    /**
     * 使用花呗分期要进行的分期数
     *
     * @param string $extendParamsHbFqNum
     *
     * @return static
     */
    public function setBizContentExtendParamsHbFqNum($extendParamsHbFqNum)
    {
        return $this->setBizContentExtendParams('hb_fq_num', $extendParamsHbFqNum);
    }

    /**
     * 使用花呗分期需要卖家承担的手续费比例的百分值，传入100代表100%
     *
     * @param string $extendParamsHbFqSellerPercent
     *
     * @return static
     */
    public function setBizContentExtendParamsHbFqSellerPercent($extendParamsHbFqSellerPercent)
    {
        return $this->setBizContentExtendParams('hb_fq_seller_percent', $extendParamsHbFqSellerPercent);
    }

    /**
     * 行业数据回流信息, 详见：地铁支付接口参数补充说明
     *
     * @param string $extendParamsIndustryRefluxInfo
     *
     * @return static
     */
    public function setBizContentExtendParamsIndustryRefluxInfo($extendParamsIndustryRefluxInfo)
    {
        return $this->setBizContentExtendParams('industry_reflux_info', $extendParamsIndustryRefluxInfo);
    }

    /**
     * 卡类型
     *
     * @param string $extendParamsCardType
     *
     * @return static
     */
    public function setBizContentExtendParamsCardType($extendParamsCardType)
    {
        return $this->setBizContentExtendParams('card_type', $extendParamsCardType);
    }

    /**
     * 商户原始订单号
     *
     * - 最大长度限制32位
     *
     * @param string $merchantOrderNo
     *
     * @return static
     */
    public function setBizContentMerchantOrderNo($merchantOrderNo)
    {
        return $this->setBizContent('merchant_order_no', $merchantOrderNo);
    }

    /**
     * 可用渠道，用户只能在指定渠道范围内支付
     *
     * - 当有多个渠道时用“,”分隔
     * - 与disable_pay_channels互斥
     *
     * @param string $enablePayChannels
     *
     * @return static
     */
    public function setBizContentEnablePayChannels($enablePayChannels)
    {
        return $this->setBizContent('enable_pay_channels', $enablePayChannels);
    }

    /**
     * 商户门店编号
     *
     * @param string $storeId
     *
     * @return static
     */
    public function setBizContentStoreId($storeId)
    {
        return $this->setBizContent('store_id', $storeId);
    }

    /**
     * 指定渠道
     *
     * - 目前仅支持传入pcredit
     * - 若由于用户原因渠道不可用，用户可选择是否用其他渠道支付
     * - 该参数不可与花呗分期参数同时传入
     *
     * @param string $specifiedChannel
     *
     * @return static
     */
    public function setBizContentSpecifiedChannel($specifiedChannel)
    {
        return $this->setBizContent('specified_channel', $specifiedChannel);
    }

    /**
     * 禁用渠道
     *
     * - 用户不可用指定渠道支付
     * - 当有多个渠道时用“,”分隔
     * - 与enable_pay_channels互斥
     *
     * @param string $disablePayChannels
     *
     * @return static
     */
    public function setBizContentDisablePayChannels($disablePayChannels)
    {
        return $this->setBizContent('disable_pay_channels', $disablePayChannels);
    }

    /**
     * 外部指定买家
     *
     * @param string $key
     * @param string $value
     *
     * @return static
     */
    public function setBizContentExtUserInfo($key, $value)
    {
        $array = I::get($this->_values, 'biz_content.ext_user_info', []);
        $array[$key] = $value;
        return $this->setBizContent('ext_user_info', $array);
    }

    /**
     * 姓名
     *
     * - need_check_info=T时该参数才有效
     *
     * @param string $extUserInfoName
     *
     * @return static
     */
    public function setBizContentExtUserInfoName($extUserInfoName)
    {
        return $this->setBizContentExtUserInfo('name', $extUserInfoName);
    }

    /**
     * 手机号
     *
     * - 该参数暂不校验
     *
     * @param string $extUserInfoMobile
     *
     * @return static
     */
    public function setBizContentExtUserInfoMobile($extUserInfoMobile)
    {
        return $this->setBizContentExtUserInfo('mobile', $extUserInfoMobile);
    }

    /**
     * 证件类型
     *
     * - 身份证：IDENTITY_CARD
     * - 护照：PASSPORT
     * - 军官证：OFFICER_CARD
     * - 士兵证：SOLDIER_CARD
     * - 户口本：HOKOU
     * - need_check_info=T时该参数才有效
     *
     * @param string $extUserInfoCertType
     *
     * @return static
     */
    public function setBizContentExtUserInfoCertType($extUserInfoCertType)
    {
        return $this->setBizContentExtUserInfo('cert_type', $extUserInfoCertType);
    }

    /**
     * 证件号
     *
     * - eed_check_info=T时该参数才有效
     *
     * @param string $extUserInfoCertNo
     *
     * @return static
     */
    public function setBizContentExtUserInfoCertNo($extUserInfoCertNo)
    {
        return $this->setBizContentExtUserInfo('cert_no', $extUserInfoCertNo);
    }

    /**
     * 允许的最小买家年龄，买家年龄必须大于等于所传数值
     *
     * - need_check_info=T时该参数才有效
     * - min_age为整数，必须大于等于0
     *
     * @param string $extUserInfoMinAge
     *
     * @return static
     */
    public function setBizContentExtUserInfoMinAge($extUserInfoMinAge)
    {
        return $this->setBizContentExtUserInfo('min_age', $extUserInfoMinAge);
    }

    /**
     * 是否强制校验付款人身份信息
     *
     * - T：强制校验
     * - F：不强制
     *
     * @param string $extUserInfoFixBuyer
     *
     * @return static
     */
    public function setBizContentExtUserInfoFixBuyer($extUserInfoFixBuyer)
    {
        return $this->setBizContentExtUserInfo('fix_buyer', $extUserInfoFixBuyer);
    }

    /**
     * 是否强制校验身份信息
     *
     * - T：强制校验
     * - F：不强制
     *
     * @param string $extUserInfoNeedCheckInfo
     *
     * @return static
     */
    public function setBizContentExtUserInfoNeedCheckInfo($extUserInfoNeedCheckInfo)
    {
        return $this->setBizContentExtUserInfo('need_check_info', $extUserInfoNeedCheckInfo);
    }

    /**
     * 商户传入业务信息
     *
     * - 具体值要和支付宝约定，应用于安全，营销等参数直传场景，格式为json格式
     *
     * @param string $businessParams
     *
     * @return static
     */
    public function setBizContentBusinessParams($businessParams)
    {
        return $this->setBizContent('business_params', $businessParams);
    }

}
