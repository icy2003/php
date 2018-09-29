<?php

namespace icy2003\ihelpers;

class Env
{
    public static function get($configName, $defaultValue = null)
    {
        $result = get_cfg_var($configName);

        return null === $result ? $defaultValue : $result;
    }

    public static function hasExt($extension)
    {
        return extension_loaded($extension);
    }
}
