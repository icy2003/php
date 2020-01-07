<?php

namespace icy2003\php\iapis;

use icy2003\php\I;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;
use icy2003\php\ihelpers\Strings;

/**
 * api 来自“今日影视”，请勿做商业用途，侵删
 */
class VideoResource extends Api
{

    /**
     * 按首字母查询视频
     *
     * @param string $firsts 首字母字符串，如果是中文，则强制转成首字母
     * @param integer $page
     * @param integer $pageSize
     *
     * @return static
     */
    public function fetchSearchFirst($firsts, $page = 0, $pageSize = 20)
    {
        $this->_result = (array)Json::get(Http::get('http://api.jinsapi.com/yingshi/search', [
            'keys' => Strings::toPinyinFirst($firsts),
            'page' => $page,
            'pageSize' => $pageSize,
        ]), 'data', []);
        $this->_toArrayCall = function ($array) {
            return Arrays::columns($array, [
                'id' => 'd_id',
                'name' => 'd_name',
                'picture' => 'd_pic',
                'tag' => 'd_remarks',
            ], 2);
        };

        return $this;
    }

    /**
     * 按关键字查询视频
     *
     * @param string $keywords
     * @param integer $page
     * @param integer $pageSize
     *
     * @return static
     */
    public function fetchSearch($keywords, $page = 0, $pageSize = 20)
    {
        $this->_result = (array)Json::get(Http::post('http://api.jinsapi.com/yingshi/searchForHanZi', [
            'keys' => $keywords,
            'page' => $page,
            'pageSize' => $pageSize,
        ]), 'data', []);
        $this->_toArrayCall = function ($array) {
            return Arrays::columns($array, [
                'id' => 'd_id',
                'name' => 'd_name',
                'picture' => 'd_pic',
                'tag' => 'd_remarks',
            ], 2);
        };

        return $this;
    }

    /**
     * 根据视频 ID 获取视频信息
     *
     * @param integer $id
     *
     * @return static
     */
    public function fetchById($id)
    {
        $this->_result = (array)Json::get(Http::get('http://api.jinsapi.com/yingshi/getVodById', [
            'd_id' => $id,
        ]), 'data', []);
        $this->_toArrayCall = function ($array) {
            return Arrays::columns($array, [
                'id' => 'd_id',
                'name' => 'd_name',
                'picture' => 'd_pic',
                'actors' => 'd_starring',
                'tag' => 'd_remarks',
                'area' => 'd_area',
                'lang' => 'd_lang',
                'year' => 'd_year',
                'description' => 'd_content',
                'episodes' => function ($array) {
                    $episodes = explode('#', I::get($array, 'd_playurl'));
                    return Arrays::explode('$', $episodes);
                },
            ], 1);
        };

        return $this;
    }

}
