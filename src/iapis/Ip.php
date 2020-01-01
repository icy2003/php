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
     * @return void
     */
    public function fetchAttribution($address)
    {
        $res = Http::get('https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php', [
            'query' => $address,
            'resource_id' => '6006',
            'oe' => 'utf8',
        ]);
        $data = Json::get($res, 'data.0.location', false);
        if (is_string($data)) {
            list($this->_result['city'], $this->_result['type']) = Arrays::lists(explode(' ', $data), 2);
        }
    }
}
