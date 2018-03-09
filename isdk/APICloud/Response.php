<?php

namespace icy2003\isdk\APICloud;

use icy2003\ihelpers\CResponse;

class Response extends CResponse
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAILURE = 0;

    public static function i($response, $format = 'json')
    {
        $iRes = parent::i($response);
        $res['code'] = $iRes['code'];
        if (isset($iRes['error'])) {
            $res['status'] = $iRes['error']['status'];
            $res['name'] = $iRes['error']['name'];
            $res['message'] = $iRes['error']['message'];
        } else {
            $res = $iRes;
            $res['status'] = self::STATUS_SUCCESS;
        }

        return $res;
    }
}
