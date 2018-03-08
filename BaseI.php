<?php

namespace icy2003;

defined('I_DEBUG') || define('I_DEBUG', false);

/**
 *
 * @filename BaseI.php
 * @encoding UTF-8
 * @author icy2003 <2317216477@qq.com>
 * @link https://github.com/icy2003
 */
class BaseI
{

    public static function autoload($className)
    {
        if (strpos($className, '\\') !== false) {
            $classFile = str_replace('\\', '/', $className) . '.php';
            if ($classFile === false || !is_file($classFile)) {
                return;
            }
        } else {
            return;
        }
        include($classFile);
        if (I_DEBUG && !class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
            throw new Exception("Unable to find '$className' in file: $classFile. Namespace missing?");
        }
    }

    public static function getVersion()
    {
        return '0.0.1';
    }

}


