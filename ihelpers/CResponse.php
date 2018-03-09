<?php

namespace icy2003\ihelpers;

class CResponse
{
    const CODE_SUCCESS = 0;
    const CODE_ERROR = -1;

    public static function i($response, $format = 'json')
    {
        if (null !== $response) {
            if ('json' === $format) {
                $res = Json::decode($response);
                $res['code'] = self::CODE_SUCCESS;
            }
        } else {
            $res = ['code' => self::CODE_ERROR, 'response' => $response];
        }

        return $res;
    }
}
