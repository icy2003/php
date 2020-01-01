<?php
/**
 * Class Api
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis;

use Exception;
use icy2003\php\I;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;

/**
 * 搜集的 API 接口
 */
class Api
{

    /**
     * API 返回原始数组
     *
     * @var array
     */
    protected $_result = [];

    /**
     * 成功判断
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return true;
    }

    /**
     * 返回错误信息
     *
     * @return array|string
     */
    public function getError()
    {
        return [];
    }

    /**
     * 获取结果
     *
     * @param string $key 如果有此参数，表示取某个属性
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
}
