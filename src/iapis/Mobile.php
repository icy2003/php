<?php
/**
 * Class Mobile
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis;

use icy2003\php\I;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;

/**
 * 手机号相关接口
 */
class Mobile extends Api
{
    /**
     * 查询手机归属地
     *
     * @param string $number 手机号码
     *
     * @return static
     */
    public function fetchAttribution($number)
    {
        $this->_result = Json::get(Http::get('https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php', [
            'resource_name' => 'guishudi',
            'query' => $number,
            'oe' => 'utf8',
        ]), 'data.0', []);
        $this->_toArrayCall = function ($array) {
            return [
                'city' => I::get($array, 'city'),
                'province' => I::get($array, 'prov'),
                'type' => I::get($array, 'type'),
            ];
        };

        return $this;
    }
}
