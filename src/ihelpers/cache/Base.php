<?php

namespace icy2003\php\ihelpers\cache;

abstract class Base
{
    protected $_cache;

    protected $_keyPrefix;

    abstract public function set($key, $value, $duration = 0);

    abstract public function get($key);

    abstract public function delete($key);

    abstract public function clear();
}
