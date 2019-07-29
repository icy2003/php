<?php
/**
 * Class Api
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis;

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
     * 查询 ip 归属地
     *
     * @param string $ip IP 地址
     *
     * @return array|false
     */
    public static function ip($ip)
    {
        $res = Http::get('https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php', [
            'query' => $ip,
            'resource_id' => '6006',
            'oe' => 'utf8',
        ]);
        $data = Json::get($res, 'data.0.location', false);
        if (is_string($data)) {
            $array = [];
            list($array['city'], $array['type']) = Arrays::lists(explode(' ', $data), 2);
            return $array;
        }
        return false;
    }

    /**
     * 查询手机归属地
     *
     * @param string $mobile
     *
     * @return array|false
     */
    public static function mobile($mobile)
    {
        $res = Http::get('https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php', [
            'resource_name' => 'guishudi',
            'query' => $mobile,
            'oe' => 'utf8',
        ]);
        $data = Json::get($res, 'data.0', false);
        if (is_array($data)) {
            return [
                'city' => I::get($data, 'city'),
                'province' => I::get($data, 'prov'),
                'type' => I::get($data, 'type'),
            ];
        }
        return false;
    }
}
