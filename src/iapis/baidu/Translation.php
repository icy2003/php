<?php

namespace icy2003\php\iapis\baidu;

use icy2003\php\I;
use icy2003\php\iapis\Api;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;
use icy2003\php\ihelpers\Strings;

class Translation extends Api
{
    /**
     * 错误码列表
     *
     * @var array
     */
    protected $_errorMap = [
        '52000' => '成功',
        '52001' => '请求超时',
        '52002' => '系统错误',
        '52003' => '未授权用户',
        '54000' => '必填参数为空',
        '54001' => '签名错误',
        '54003' => '访问频率受限',
        '54004' => '账户余额不足',
        '54005' => '长 query 频繁请求',
        '58000' => '客户端 IP 非法',
        '58001' => '译文语言方向不支持',
        '58002' => '服务当前关闭',
        '90107' => '认证未通过或未生效',
    ];

    /**
     * APP ID
     *
     * @link https://api.fanyi.baidu.com/api/trans/product/desktop?req=developer
     *
     * @var string
     */
    protected $_appid;

    /**
     * 密钥
     *
     * @link https://api.fanyi.baidu.com/api/trans/product/desktop?req=developer
     *
     * @var string
     */
    protected $_secretKey;

    /**
     * 初始化
     *
     * @param string $appid
     * @param string $secretKey
     */
    public function __construct($appid, $secretKey)
    {
        $this->_appid = $appid;
        $this->_secretKey = $secretKey;
    }

    /**
     * 通用翻译 API
     *
     * @param string $text
     * @param string $to
     *
     * @return static
     */
    public function general($text, $to)
    {
        $salt = Strings::random();
        $this->_result = Json::decode(Http::get('http://api.fanyi.baidu.com/api/trans/vip/translate', [
            'q' => $text,
            'from' => 'auto',
            'to' => $to,
            'appid' => $this->_appid,
            'salt' => $salt,
            'sign' => md5($this->_appid . $text . $salt . $this->_secretKey),
        ]));
        $this->_toArrayCall = function ($array) {
            $trans = I::get($array, 'trans_result', []);
            return Arrays::column($trans, 'dst');
        };

        return $this;
    }

    /**
     * @ignore
     *
     * @return boolean
     */
    public function isSuccess()
    {
        if ($this->_result['error_code'] == 52000) {
            return true;
        }
        return false;
    }

    /**
     * @ignore
     *
     * @return string
     */
    public function getError()
    {
        return I::get($this->_errorMap, $this->_result['error_code'], $this->_result['error_msg']);
    }
}
