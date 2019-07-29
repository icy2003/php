<?php
/**
 * Class Base
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis\baidu;

use Exception;
use icy2003\php\I;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;

/**
 * 百度 API 基类
 */
class Base
{
    /**
     * API KEY
     *
     * @var string
     */
    protected $_apiKey;

    /**
     * secret KEY
     *
     * @var string
     */
    protected $_secretKey;

    /**
     * 错误信息
     *
     * @var array
     */
    protected $_errorMap = [];

    /**
     * 构造函数
     *
     * @param string $apiKey
     * @param string $secretKey
     */
    public function __construct($apiKey, $secretKey)
    {
        $this->_apiKey = $apiKey;
        $this->_secretKey = $secretKey;
        $this->_errorMap = [
            'unknown client id' => 'API Key 不正确',
            'Client authentication failed' => 'Secret Key不正确',
        ];
    }

    /**
     * API 返回原始数组
     *
     * @var array
     */
    protected $_result;

    /**
     * access_token
     *
     * @var string
     */
    protected $_token;

    /**
     * 请求获得 access_token
     *
     * @return static
     */
    public function requestToken()
    {
        if (null === $this->_token) {
            $this->_result = Json::decode(Http::post('https://aip.baidubce.com/oauth/2.0/token', [], [
                'grant_type' => 'client_credentials',
                'client_id' => $this->_apiKey,
                'client_secret' => $this->_secretKey,
            ]));
            $this->_token = $this->getResult(self::RESULT_TOKEN);
            if (null === $this->_token) {
                throw new Exception("access_token 获取失败");
            }
        }
        return $this;
    }

    /**
     * 是否成功
     *
     * @return boolean
     */
    public function isSuccess()
    {
        if (I::get($this->_result, 'error')) {
            return false;
        }
        return true;
    }

    /**
     * 获取错误信息
     *
     * @return string
     */
    public function getError()
    {
        if ($error = I::get($this->_result, 'error_description')) {
            return I::get($this->_errorMap, $error);
        }

        return '未知错误';
    }

    /**
     * access_token 键
     */
    const RESULT_TOKEN = 'access_token';

    /**
     * 获取结果
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getResult($key = null)
    {
        if ($this->isSuccess()) {
            if (null === $key) {
                return $this->_result;
            } else {
                return I::get($this->_result, $key);
            }
        }
        throw new Exception($this->getError());
    }

    /**
     * toArray 时调用的函数
     *
     * @var callback
     */
    protected $_toArrayCall;

    /**
     * 智能返回有效数据
     *
     * - 如果数据缺失，请使用 getResult() 获取原始数据
     *
     * @return array
     */
    public function toArray()
    {
        return I::trigger($this->_toArrayCall, [$this->getResult()]);
    }
}
