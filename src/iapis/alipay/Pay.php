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
use icy2003\php\ihelpers\Request;
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
        if (null === $this->_appId) {
            throw new Exception("缺少应用 ID");
        }
        $this->_rsaPrivateKey = $rsaPrivateKey;
        if (null === $this->_rsaPrivateKey) {
            throw new Exception("缺少商户私钥");
        }
    }

    /**
     * 支付类型：APP
     */
    const TRADE_TYPE_APP = 'APP';

    /**
     * 支付接口
     *
     * - APP支付：返回的字符串需给客户端拉起支付宝
     *
     * @return static
     */
    public function pay()
    {
        if (null === $this->_tradeType) {
            throw new Exception('请使用 setTradeType 定义支付类型');
        }
        // APP 支付
        if (self::TRADE_TYPE_APP === $this->_tradeType) {
            if (null === I::get($this->_values, 'biz_content.total_amount')) {
                throw new Exception('请使用 setBizContentTotalAmount 设置 biz_content.total_amount');
            }
            if (null === I::get($this->_values, 'biz_content.subject')) {
                throw new Exception('请使用 setBizContentSubject 设置：biz_content.subject');
            }
            if (null === I::get($this->_values, 'biz_content.out_trade_no')) {
                throw new Exception('请使用 setBizContentOutTradeNo 设置：biz_content.out_trade_no');
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
        }

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
                $array[] = $key . '=' . Charset::convertTo($value, (string)I::get($this->_values, 'charset', 'utf-8'));
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
     * 返回刚刚调用过的支付宝接口的结果
     *
     * @return mixed
     */
    public function getRes()
    {
        return $this->_result;
    }

    /**
     * 支付结果通知以及退款结果通知的数据处理
     *
     * 请参考[交易状态问题解析](https://openclub.alipay.com/club/history/read/5407)
     *
     * @return array
     */
    public function getNotifyArray()
    {
        return (new Request())->post();
    }

    /**
     * 返回通知成功时发送给支付宝的字符串
     *
     * @return string
     */
    public function getNotifyReturn()
    {
        return 'success';
    }

    /**
     * self::getNotifyArray 和 self::getNotifyReturn 的结合：通知为交易成功时，$callback 为 true，则输出成功给微信
     *
     * - 回调参数：
     *      1. 通知数组：$array 支付宝返回的原始数据
     *      2. 是否支付成功：$isPay
     *      3. 是否退款成功：$isRefund
     *      4. 是否全额退款：$isRefundFull 退款金额（refund_fee）永远和总金额（total_amount）对比，不考虑优惠，如需计算优惠，请直接使用 $array 原始数据自行计算
     * - 回调函数返回 true 时才会输出成功给支付宝
     *
     * @param callback $callback 回调函数
     *
     * @return void
     * @info 此函数之后不得有任何输出
     */
    public function notify($callback = null)
    {
        $array = $this->getNotifyArray();
        if (!empty($array)) {
            $isPay = $isRefund = $isRefundFull = null;
            $tradeStatus = I::get($array, 'trade_status');
            $refundFee = I::get($array, 'refund_fee');
            $totalAmount = I::get($array, 'total_amount');
            if ($refundFee > 0) {
                $isRefund = true;
                if ('TRADE_CLOSED' === $tradeStatus && $refundFee === $totalAmount) {
                    // 3、交易成功后，交易全额退款交易状态转为TRADE_CLOSED（交易关闭）
                    // 7、如果一直部分退款退完所有交易金额则交易状态转为TRADE_CLOSED（交易关闭）
                    $isRefundFull = true;
                } elseif ('TRADE_SUCCESS' === $tradeStatus && $refundFee < $totalAmount) {
                    // 6、交易成功后部分退款，交易状态仍为TRADE_SUCCESS（交易成功）
                    $isRefundFull = false;
                }
            } elseif ($totalAmount > 0) {
                if ('TRADE_CLOSED' === $tradeStatus) {
                    // 2、交易创建成功后，用户未付款交易超时关闭交易状态转为TRADE_CLOSED（交易关闭）
                    $isPay = false;
                } elseif ('TRADE_SUCCESS' === $tradeStatus || 'TRADE_FINISHED' === $tradeStatus) {
                    // 1、交易创建成功后，用户支付成功，交易状态转为TRADE_SUCCESS（交易成功）
                    // 4、交易创建成功后，用户支付成功后，若用户商品不支持退款，交易状态直接转为TRADE_FINISHED（交易完成）
                    // 5、交易成功后，默认退款时间三个月内没有退款，交易状态转为TRADE_FINISHED（交易完成）不可退款
                    // 8、如果未退完所有交易金额，三个月后交易状态转为TRADE_FINISHED（交易完成）不可退款
                    $isPay = true;
                }
            }
            if (null === $callback || true === I::call($callback, [$array, $isPay, $isRefund, $isRefundFull])) {
                echo $this->getNotifyReturn();
            }
        }
    }

}
