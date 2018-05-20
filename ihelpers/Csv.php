<?php

namespace icy2003\ihelpers;

class Csv
{
    private $_array = [];
    private $_csv;

    public function fromString($csv)
    {
        $this->_csv = $csv;

        return $this;
    }

    public function toArray()
    {
        $this->_array = explode(PHP_EOL, $this->_csv);

        return $this->_array;
    }
}
