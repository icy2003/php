<?php
/**
 * Class OCR
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis\baidu;

use icy2003\php\I;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;

/**
 * 图像识别
 *
 * @link https://ai.baidu.com/docs#/OCR-API/top
 */
class OCR extends Base
{

    /**
     * 选项列表
     *
     * @var array
     */
    protected $_options = [
        'image' => null,
        'language_type' => 'CHN_ENG',
        'detect_direction' => false,
        'detect_language' => false,
        'probability' => false,
        'recognize_granularity' => 'big',
        'vertexes_location' => false,
        'words_type' => null,
    ];

    /**
     * 设置选项
     *
     * @param array $options
     * - image：图片 base64
     * - language_type：识别语言类型，默认为CHN_ENG。可选值包括：CHN_ENG：中英文混合；ENG：英文；POR：葡萄牙语；FRE：法语；GER：德语；ITA：意大利语；SPA：西班牙语；RUS：俄语；JAP：日语；KOR：韩语
     * - detect_direction：是否检测图像朝向，默认不检测，即：false。朝向是指输入图像是正常方向、逆时针旋转90/180/270度。可选值包括：true，检测朝向；false，不检测朝向
     * - detect_language：是否检测语言，默认不检测。当前支持（中文、英语、日语、韩语）
     * - probability：是否返回识别结果中每一行的置信度
     * - recognize_granularity：是否定位单字符位置，big：不定位单字符位置，默认值；small：定位单字符位置
     * - vertexes_location：是否返回文字外接多边形顶点位置，不支持单字位置。默认为false
     *
     * @return static
     */
    public function setOptions($options)
    {
        return parent::setOptions($options);
    }

    /**
     * 通用文字识别
     *
     * 用户向服务请求识别某张图中的所有文字
     * - setOptions()：image、language_type、detect_direction、detect_language、probability
     *
     * @return static
     */
    public function generalBasic()
    {
        $this->requestToken();
        $this->_result = Json::decode(Http::post('https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic', Arrays::some($this->_options, [
            'image',
            'language_type',
            'detect_direction',
            'detect_language',
            'probability',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return Arrays::column((array) I::get($result, 'words_result', []), 'words');
        };

        return $this;
    }

    /**
     * 通用文字识别（高精度版）
     *
     * 用户向服务请求识别某张图中的所有文字，相对于通用文字识别该产品精度更高，但是识别耗时会稍长
     * - setOptions()：image、detect_direction、probability
     *
     * @param array $options
     * @return static
     */
    public function accurateBasic()
    {
        $this->requestToken();
        $this->_result = Json::decode(Http::post('https://aip.baidubce.com/rest/2.0/ocr/v1/accurate_basic', Arrays::some($this->_options, [
            'image',
            'detect_direction',
            'probability',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return Arrays::column((array) I::get($result, 'words_result', []), 'words');
        };

        return $this;
    }

    /**
     * 通用文字识别（含位置信息版）
     *
     * 用户向服务请求识别某张图中的所有文字，并返回文字在图中的位置信息
     *
     * @param array $options
     * - setOptions()：image、recognize_granularity、language_type、detect_direction、detect_language、vertexes_location、probability
     * @return static
     */
    public function general()
    {
        $this->requestToken();
        $this->_result = Json::decode(Http::post('https://aip.baidubce.com/rest/2.0/ocr/v1/general', Arrays::some($this->_options, [
            'image',
            'recognize_granularity',
            'language_type',
            'detect_direction',
            'detect_language',
            'vertexes_location',
            'probability',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result');
        };

        return $this;
    }

    /**
     * 通用文字识别（高精度含位置版）
     *
     * 用户向服务请求识别某张图中的所有文字，并返回文字在图片中的坐标信息，相对于通用文字识别（含位置信息版）该产品精度更高，但是识别耗时会稍长
     *
     * @param array $options
     * - setOptions()：image、recognize_granularity、detect_direction、vertexes_location、probability
     * @return static
     */
    public function accurate()
    {
        $this->requestToken();
        $this->_result = Json::decode(Http::post('https://aip.baidubce.com/rest/2.0/ocr/v1/accurate', Arrays::some($this->_options, [
            'image',
            'recognize_granularity',
            'detect_direction',
            'vertexes_location',
            'probability',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result');
        };

        return $this;
    }

    /**
     * 手写文字识别
     *
     * 对手写中文汉字、数字进行识别
     *
     * @param array $options
     * - setOptions()：image、recognize_granularity、words_type
     * @return static
     */
    public function handwriting()
    {
        $this->requestToken();
        $this->_result = Json::decode(Http::post('https://aip.baidubce.com/rest/2.0/ocr/v1/handwriting', Arrays::some($this->_options, [
            'image',
            'recognize_granularity',
            'words_type',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'words_result');
        };

        return $this;
    }

    /**
     * 身份证识别
     *
     * 支持对大陆居民二代身份证正反面的所有字段进行结构化识别，包括姓名、性别、民族、出生日期、住址、身份证号、签发机关、有效期限；同时，支持对用户上传的身份证图片进行图像风险和质量检测，可识别图片是否为复印件或临时身份证，是否被翻拍或编辑，是否存在正反颠倒、模糊、欠曝、过曝等质量问题
     *
     * @param array $options
     * - id_card_side：front：身份证含照片的一面；back：身份证带国徽的一面
     * - detect_direction：是否检测图像朝向，默认不检测，即：false。朝向是指输入图像是正常方向、逆时针旋转90/180/270度。可选值包括：true，检测朝向；false，不检测朝向
     * - detect_risk：是否开启身份证风险类型(身份证复印件、临时身份证、身份证翻拍、修改过的身份证)功能，默认不开启，即：false。可选值:true-开启；false-不开启
     * @return static
     */
    public function idcard($options = [
        'id_card_side' => 'front',
        'detect_direction' => false,
        'detect_risk' => false,
    ]) {
        $options = Arrays::merge($this->_options, $options);
        $this->requestToken();
        $this->_result = Json::decode(Http::post('https://aip.baidubce.com/rest/2.0/ocr/v1/idcard', $options, [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            $return = [];
            $words = I::get($result, 'words_result', []);
            foreach ($words as $name => $word) {
                $return[$name] = I::get($word, 'words');
            }
            return $return;
        };

        return $this;
    }

    /**
     * 银行卡识别
     *
     * 识别银行卡并返回卡号、有效期、发卡行和卡片类型
     *
     * @return static
     */
    public function bankcard()
    {
        $options = $this->_options;
        $this->requestToken();
        $this->_result = Json::decode(Http::post('https://aip.baidubce.com/rest/2.0/ocr/v1/bankcard', $options, [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function ($result) {
            return I::get($result, 'result');
        };
        return $this;
    }

}
