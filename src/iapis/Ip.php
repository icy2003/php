<?php
/**
 * Class Ip
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis;

use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;

/**
 * Ip 相关接口
 */
class Ip extends Api
{
    /**
     * 查询 Ip 归属地
     *
     * @param string $address IP 地址
     *
     * @return static
     */
    public function fetchAttribution($address)
    {
        $this->_result = Json::get(Http::get('https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php', [
            'query' => $address,
            'resource_id' => '6006',
            'oe' => 'utf8',
        ]), 'data.0.location', false);
        $this->_toArrayCall = function ($array) {
            list($return['city'], $return['type']) = Arrays::lists(explode(' ', $array), 2);
            return $return;
        };

        return $this;
    }
}
