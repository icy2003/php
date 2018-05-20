<?php

namespace icy2003\isdks\Wechat;

use Exception;

class WxException extends Exception
{
    public function errorMessage()
    {
        return $this->getMessage();
    }
}
