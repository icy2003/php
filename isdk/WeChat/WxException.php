<?php

namespace icy2003\isdk\Wechat;

class WxException extends Exception
{
    public function errorMessage()
    {
        return $this->getMessage();
    }
}
