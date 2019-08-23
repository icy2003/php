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
use GuzzleHttp\Exception\ClientException;
use icy2003\php\I;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;
use icy2003\php\ihelpers\Base64;
use icy2003\php\icomponents\file\LocalFile;
use icy2003\php\ihelpers\Arrays;

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
            0 => '成功',
            1 => '服务器内部错误，请再次请求',
            2 => '服务暂不可用，请再次请求',
            3 => '调用的 API 不存在，请检查后重新尝试',
            4 => '集群超限额',
            6 => '无权限访问该用户数据',
            13 => '获取 token 失败',
            14 => 'IAM鉴权失败',
            15 => '应用不存在或者创建失败',
            17 => '每天请求量超限额',
            18 => 'QPS 超限额',
            19 => '请求总量超限额',
            100 => '包含了无效或错误参数，请检查代码',
            110 => 'Access Token 失效',
            111 => 'Access token 过期',
            216100 => '请求中包含非法参数，请检查后重新尝试',
            216101 => '缺少必须的参数，请检查参数是否有遗漏',
            216102 => '请求了不支持的服务，请检查调用的 url',
            216103 => '请求中某些参数过长，请检查后重新尝试',
            216110 => 'appid 不存在，请重新核对信息是否为后台应用列表中的 appid',
            216200 => '图片为空，请检查后重新尝试',
            216201 => '上传的图片格式错误，现阶段我们支持的图片格式为：PNG、JPG、JPEG、BMP，请进行转码或更换图片',
            216202 => '上传的图片大小错误，现阶段我们支持的图片大小为：base64编码后小于4M，分辨率不高于4096*4096，请重新上传图片',
            216203 => '上传的图片 base64 编码有误，请校验 base64 编码方式，并重新上传图片',
            216630 => '识别错误，请再次请求',
            216631 => '识别银行卡错误，出现此问题的原因一般为：您上传的图片非银行卡正面，上传了异形卡的图片或上传的银行卡正品图片不完整',
            216633 => '识别身份证错误，出现此问题的原因一般为：您上传了非身份证图片或您上传的身份证图片不完整',
            216634 => '检测错误，请再次请求',
            282000 => '服务器内部错误，请再次请求',
            282002 => '编码错误，请使用GBK编码',
            282003 => '请求参数缺失',
            282004 => '请求中包含非法参数，请检查后重新尝试',
            282005 => '处理批量任务时发生部分或全部错误，请根据具体错误码排查',
            282006 => '批量任务处理数量超出限制，请将任务数量减少到 10 或 10 以下',
            282008 => '仅支持 GBK 和 UTF-8，其余为不支持的字符编码，请检查后重新尝试',
            282011 => '未训练或未生效该接口',
            282100 => '图片压缩转码错误',
            282101 => '长图片切分数量超限',
            282102 => '未检测到图片中识别目标',
            282103 => '图片目标识别错误',
            282114 => 'URL 长度超过 1024 字节或为 0',
            282130 => '当前查询无结果返回，出现此问题的原因一般为：参数配置存在问题，请检查后重新尝试',
            282131 => '输入长度超限，请查看文档说明',
            282133 => '接口参数缺失',
            282134 => '输入为空',
            282300 => 'word 不在算法词典中',
            282301 => 'word_1 提交的词汇暂未收录，无法比对相似度',
            282302 => 'word_2 提交的词汇暂未收录，无法比对相似度',
            282303 => 'word_1和word_2暂未收录，无法比对相似度',
            282808 => 'request id 不存在',
            282809 => '返回结果请求错误（不属于 excel 或 json）',
            282810 => '图像识别错误',
            283300 => '入参格式有误，可检查下图片编码、代码格式是否有误',
            336000 => '服务器内部错误，请再次请求',
            336001 => '入参格式有误，比如缺少必要参数、图片base64编码错误等等，可检查下图片编码、代码格式是否有误',
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
     * 设置 access_token
     *
     * @param string $token
     *
     * @return static
     */
    public function setToken($token)
    {
        $this->_token = $token;

        return $this;
    }

    /**
     * 请求获得 access_token
     *
     * @return static
     */
    public function requestToken()
    {
        if (null === $this->_token) {
            try {
                $this->_result = Json::decode(Http::post('https://aip.baidubce.com/oauth/2.0/token', [], [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->_apiKey,
                    'client_secret' => $this->_secretKey,
                ]));
            } catch (ClientException $e) {
                throw new Exception("access_token 获取失败，接口返回为：" . $e->getResponse()->getBody()->getContents());
            }
            $this->_token = (string)$this->getResult(self::RESULT_TOKEN);
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
        if (I::get($this->_result, 'error') || I::get($this->_result, 'error_code') > 0) {
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
        return (string)I::get($this->_errorMap, I::get($this->_result, 'error_code', 0), '未知错误');
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
        return (array)I::call($this->_toArrayCall, [$this->getResult()]);
    }

    /**
     * 选项列表
     *
     * @var array
     */
    protected $_options = [];

    /**
     * 设置选项
     *
     * @param array $options
     *
     * @return static
     */
    public function setOptions($options)
    {
        $this->_options = Arrays::merge($this->_options, $options);
        return $this;
    }

    /**
     * toString 魔术方法
     *
     * @return string
     */
    public function __toString()
    {
        return Json::encode($this->_result);
    }

    /**
     * 加载一个图片
     *
     * 可支持格式：
     * - base64：图像数据，大小不超过4M，最短边至少15px，最长边最大4096px,支持jjpg/jpeg/png/bmp格式
     * - 文件 URL：图片完整URL，URL长度不超过1024字节，对应的 base64 数据限制如上，不支持https的图片链接
     *
     * @param string $image
     *
     * @return static
     */
    public function image($image)
    {
        if (Base64::isBase64($image)) {
            $this->_options['image'] = $image;
        } elseif ((new LocalFile())->isFile($image)) {
            $this->_options['image'] = Base64::fromFile($image);
        } else {
            throw new Exception('错误的图片类型');
        }
        return $this;
    }

    /**
     * 加载一段文字
     *
     * 可支持的格式：
     * - $text 为字符串：设置 text
     * - $text 为数组：设置 word_1 和 word_2
     *
     * @param string|array $text
     *
     * @return static
     */
    public function text($text)
    {
        if (is_string($text)) {
            $this->_options['text'] = $text;
        } elseif (is_array($text)) {
            $this->_options['word_1'] = I::get($text, 0);
            $this->_options['word_2'] = I::get($text, 1);
        }

        return $this;
    }
}
