<?php

namespace icy2003\ihelpers;

class Reflector
{
    public static function getConstants($object)
    {
        $reflect = new \ReflectionClass($object);

        return $reflect->getConstants();
    }

    public static function hasConstant($object, $name)
    {
        $reflect = new \ReflectionClass($object);

        return $reflect->hasConstant($name);
    }
}
