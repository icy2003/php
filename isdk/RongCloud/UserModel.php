<?php

namespace icy2003\isdk\RongCloud;

use icy2003\ihelpers\Json;

class UserModel extends RongCloud
{
    const METHOD_USER_GETTOKEN = 'user/getToken.json';

    public function token($userId, $name, $portraitUri)
    {
        $postBody['userId'] = $userId;
        $postBody['name'] = $name;
        $postBody['portraitUri'] = $portraitUri;

        return $this->post(parent::URL_IM.self::METHOD_USER_GETTOKEN, $postBody);
    }
}
