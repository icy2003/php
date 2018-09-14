<?php

namespace icy2003\ihelpers;

class Env
{
    public static function get($configName, $defaultValue = null)
    {
        $result = get_cfg_var($configName);

        return null === $result ? $defaultValue : $result;
    }
}
