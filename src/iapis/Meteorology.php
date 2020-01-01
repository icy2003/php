<?php

namespace icy2003\php\iapis;

use icy2003\php\iapis\Api;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;

/**
 * 气象（meteorology）接口
 * @link https://blog.csdn.net/weixin_34410662/article/details/93535017
 */
class Meteorology extends Api
{
    /**
     * 获取省份代码
     *
     * @return void
     */
    public function fetchProvinces()
    {
        $this->fetchCitys();
        $this->_toArrayCall = function ($array) {
            return Arrays::columns($array, ['code', 'name']);
        };
    }

    /**
     * 获取城市列表
     *
     * @param string|null $provinceCode 省份代码
     *
     * @return void
     */
    public function fetchCitys($provinceCode = null)
    {
        $res = Http::get('http://www.nmc.cn/f/rest/province/' . $provinceCode);
        $this->_result = Json::decode($res);
        $this->_toArrayCall = function ($array) {
            return Arrays::columns($array, ['code', 'city', 'province']);
        };
    }

    /**
     * 获取天气状况
     * - publish_time：更新时间（2020-01-01 19:35）
     * - airpressure：气压（hPa）
     * - feelst：体感温度（℃）
     * - humidity：相对湿度（%）
     * - icomfort：舒适度
     *      - 温暖，较舒适：1
     *      - 舒适，最可接受：0
     *      - 凉爽，较舒适：-1
     *      - 凉，不舒适：-2
     *      - 冷，很不舒适：-3
     *      - 很冷，极不适应：-4
     * - info：天气（如：晴）
     * - rain：降水（mm）
     * - temperature：气温（℃）
     * - direct：风向（东南风）
     * - power：风强（微风、1 级……）
     *
     * @param string $cityId 城市 ID
     *
     * @return void
     */
    public function fetchWeather($cityId)
    {
        $res = Http::get('http://www.nmc.cn/f/rest/real/' . $cityId);
        $this->_result = Json::decode($res);
        $this->_toArrayCall = function ($array) {
            return Arrays::columns($array, [
                'publish_time',
                'airpressure' => 'weather.airpressure',
                'feelst' => 'weather.feelst',
                'humidity' => 'weather.humidity',
                'info' => 'weather.info',
                'rain'=>'weather.rain',
                'temperature' => 'weather.temperature',
                'direct' => 'wind.direct',
                'power' => 'wind.power',
            ], 1);
        };
    }

    /**
     * 空气质量
     * - forecasttime：发布时间（2020-01-01 19:00）
     * - aq：空气质量
     *      - 优：1
     *      - 良：2
     *      - 轻度污染：3
     *      - 中度污染：4
     * - text：空气质量文本
     *
     * @param string $cityId 城市 ID
     *
     * @return void
     */
    public function fetchAirQuality($cityId)
    {
        $res = Http::get('http://www.nmc.cn/f/rest/aqi/' . $cityId);
        $this->_result = Json::decode($res);
        $this->_toArrayCall = function ($array) {
            return Arrays::columns($array, [
                'forecasttime',
                'aq',
                'text'
            ], 1);
        };
    }

    /**
     * 今天 24 小时实况
     *  - time：时间（2020-01-01 20:00）
     *  - humidity：相对湿度（%）
     *  - pressure：气压（hPa）
     *  - rain：降水（mm）
     *  - temperature：温度（℃）
     *  - windDirection：风向（？）
     *  - windSpeed：风速（？）
     *
     * @param string $cityId 城市 ID
     *
     * @return void
     */
    public function fetchToday($cityId){
        $res = Http::get('http://www.nmc.cn/f/rest/passed/' . $cityId);
        $this->_result = Json::decode($res);
        $this->_toArrayCall = function ($array) {
            return Arrays::columns($array, [
                'time',
                'humidity',
                'pressure',
                'rain'=>'rain1h',
                'temperature',
                'windDirection',
                'windSpeed'
            ], 2);
        };
    }
}
