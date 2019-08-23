<?php
/**
 * Class NLP
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis\baidu;

use Exception;
use icy2003\php\C;
use icy2003\php\I;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;
use icy2003\php\ihelpers\Strings;

/**
 * 自然语言处理
 *
 * @link https://ai.baidu.com/docs#/NLP-Basic-API/top
 */
class NLP extends Base
{

    /**
     * 选项列表
     *
     * @var array
     */
    protected $_options = [
        'text' => null,
        'word_1' => null,
        'word_2' => null,
        'mode' => 0,
    ];

    /**
     * 设置选项
     *
     * @param array $options
     * - text：待分析文本，长度限制根据不同接口定
     * - word_1：词 1，最大 64 字节
     * - word_2：词 2，最大 64 字节
     * - mode：模型选择。默认值为0，可选值mode=0（对应web模型）；mode=1（对应query模型），默认为 0
     *      1. Query模型：该模型的训练数据来源于用户在百度的日常搜索数据，适用于处理信息需求类的搜索或口语query。例如：手机缝隙灰尘怎么清除
     *      2. Web模型：该模型的训练数据来源于全网网页数据，适用于处理网页文本等书面表达句子。例如：一般而言,股份的表现形式可以是股票、股权份额等等
     *
     * @return static
     */
    public function setOptions($options)
    {
        return parent::setOptions($options);
    }

    /**
     * 词法分析（通用版）
     *
     * - 向用户提供分词、词性标注、专名识别三大功能
     * - 能够识别出文本串中的基本词汇（分词），对这些词汇进行重组、标注组合后词汇的词性，并进一步识别出命名实体
     * - setOptions()：text（限制为 20000 字节）
     *
     * @return static
     */
    public function lexer()
    {
        C::assertTrue(Strings::byteLength($this->_options['text']) <= 20000, '文字太长，不允许超过 20000 字节');
        $this->requestToken();
        $this->_result = Json::decode(Http::body('https://aip.baidubce.com/rpc/2.0/nlp/v1/lexer', Json::encode(Arrays::some($this->_options, [
            'text',
        ])), [
            'access_token' => $this->_token,
            'charset' => 'UTF-8',
        ]));

        $this->_toArrayCall = function($result) {
            return Arrays::column((array)I::get($result, 'items', []), 'item');
        };

        return $this;
    }

    /**
     * 依存句法分析
     *
     * - 依存句法分析接口可自动分析文本中的依存句法结构信息，利用句子中词与词之间的依存关系来表示词语的句法结构信息（如“主谓”、“动宾”、“定中”等结构关系），并用树状结构来表示整句的结构（如“主谓宾”、“定状补”等）
     * - setOptions()：text（限制为 256 字节）、mode
     *
     * @return static
     */
    public function depparser()
    {
        if (Strings::byteLength($this->_options['text']) > 256) {
            throw new Exception('文字太长，不允许超过 256 字节');
        }
        $this->requestToken();
        $this->_result = Json::decode(Http::body('https://aip.baidubce.com/rpc/2.0/nlp/v1/depparser', Json::encode(Arrays::some($this->_options, [
            'text',
            'mode',
        ])), [
            'access_token' => $this->_token,
            'charset' => 'UTF-8',
        ]));
        $this->_toArrayCall = function($result) {
            return Arrays::column((array)I::get($result, 'items', []), 'word');
        };

        return $this;
    }

    /**
     * 词义相似度
     *
     * - 输入两个词，得到两个词的相似度结果
     * - setOptions()：word_1、word_2
     *
     * @return static
     */
    public function wordSim()
    {
        if (Strings::byteLength($this->_options['word_1']) > 64) {
            throw new Exception('词 1 太长，不允许超过 64 字节');
        }
        if (Strings::byteLength($this->_options['word_2']) > 64) {
            throw new Exception('词 2 太长，不允许超过 64 字节');
        }
        $this->requestToken();
        $this->_result = Json::decode(Http::body('https://aip.baidubce.com/rpc/2.0/nlp/v2/word_emb_sim', Json::encode(Arrays::some($this->_options, [
            'word_1',
            'word_2',
        ])), [
            'access_token' => $this->_token,
            'charset' => 'UTF-8',
        ]));
        $this->_toArrayCall = function($result) {
            return I::get($result, 'score');
        };

        return $this;
    }
}
