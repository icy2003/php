<?php

namespace icy2003\php\iextensions\zetacomponents;

use ezcMailPartParser as iEzcMailPartParser;

class ezcMailPartParser extends iEzcMailPartParser
{

    protected function parseHeader($line, ezcMailHeadersHolder $headers)
    {
        $matches = array();
        preg_match_all("/^([\w-_]*):\s?(.*)/", $line, $matches, PREG_SET_ORDER);
        if (count($matches) > 0) {
            if (!in_array(strtolower($matches[0][1]), self::$uniqueHeaders)) {
                $arr = $headers[$matches[0][1]];
                $arr[0][] = str_replace("\t", " ", trim($matches[0][2]));
                $headers[$matches[0][1]] = $arr;
            } else {
                $headers[$matches[0][1]] = str_replace("\t", " ", trim($matches[0][2]));
            }
            $this->lastParsedHeader = $matches[0][1];
        } else if ($this->lastParsedHeader !== null) // take care of folding
        {
            if (!in_array(strtolower($this->lastParsedHeader), self::$uniqueHeaders)) {
                $arr = $headers[$this->lastParsedHeader];
                $arr[0][count($arr[0]) - 1] .= str_replace("\t", " ", $line);
                $headers[$this->lastParsedHeader] = $arr;
            } else {
                $headers[$this->lastParsedHeader] .= str_replace("\t", " ", $line);
            }
        }
        // else -invalid syntax, this should never happen.
    }
}
