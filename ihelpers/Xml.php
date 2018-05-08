<?php

namespace icy2003\ihelpers;

class Xml
{
    private $_array = [];
    private $_xml;

    public function fromString($xml)
    {
        $this->_xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        return $this;
    }

    public function toArray()
    {
        if ($this->_xml) {
            foreach ($_xml->children() as $child) {
                $this->_array[$child->getName()] = (string) $child;
            }
        }

        return $this->_array;
    }
}
