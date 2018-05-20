<?php

namespace icy2003\ihelpers;

class Data
{
    private $_data = [];

    public function count()
    {
        return count($this->_data);
    }

    public function get($name, $default = null)
    {
        $name = strtolower($name);

        return isset($this->_data[$name]) ? $this->_data[$name] : $default;
    }

    public function set($name, $value)
    {
        $name = strtolower($name);
        $this->_data[$name] = $value;

        return $this;
    }

    public function add($name, $value)
    {
        $name = strtolower($name);
        $this->_data[$name][] = $value;

        return $this;
    }

    public function isExist($name)
    {
        $name = strtolower($name);

        return isset($this->_data[$name]);
    }

    public function remove($name)
    {
        $name = strtolower($name);
        if (isset($this->_data[$name])) {
            $value = $this->_data[$name];
            unset($this->_data[$name]);

            return $value;
        }

        return null;
    }

    public function removeAll()
    {
        $this->_data = [];
    }

    public function getData()
    {
        return $this->_data;
    }
}
