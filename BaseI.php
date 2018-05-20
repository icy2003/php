<?php

namespace icy2003;

defined('I_DEBUG') || define('I_DEBUG', false);

/**
 * @filename BaseI.php
 * @encoding UTF-8
 *
 * @author icy2003 <2317216477@qq.com>
 *
 * @see https://github.com/icy2003
 */
class BaseI
{
    public static function autoload($className)
    {
        if (false !== strpos($className, '\\')) {
            $classFile = dirname(__DIR__).'/'.str_replace('icy2003', 'icy2003_php', str_replace('\\', '/', $className).'.php');
            if (false === $classFile || !is_file($classFile)) {
                return;
            }
        } else {
            return;
        }
        include $classFile;
        if (I_DEBUG && !class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
            throw new Exception("Unable to find '$className' in file: $classFile. Namespace missing?");
        }
    }

    public static function getVersion()
    {
        return '0.0.1';
    }
}
