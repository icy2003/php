<?php

namespace icy2003\php\iextensions\zetacomponents;

use ezcBaseValueException;
use ezcMailParser as GlobalEzcMailParser;
use ezcMailParserOptions;
use ezcMailParserSet as GlobalEzcMailParserSet;

class ezcMailParser extends GlobalEzcMailParser
{
    public function __construct($options = array())
    {
        if ($options instanceof ezcMailParserOptions) {
            $this->options = $options;
        } else if (is_array($options)) {
            $this->options = new ezcMailParserOptions($options);
        } else {
            throw new ezcBaseValueException("options", $options, "ezcMailParserOptions|array");
        }
    }

    private $partParser = null;

    public function parseMail(GlobalEzcMailParserSet $set, $class = null)
    {
        $mail = [];
        if (!$set->hasData()) {
            return $mail;
        }
        if ($class === null) {
            $class = $this->options->mailClass;
        }
        do {
            $this->partParser = new ezcMailRfc822Parser();
            $data = "";
            $size = 0;
            while (($data = $set->getNextLine()) !== null) {
                $this->partParser->parseBody($data);
                $size += strlen($data);
            }
            $part = $this->partParser->finish($class);
            $part->size = $size;
            $mail[] = $part;
        } while ($set->nextMail());
        return $mail;
    }
}
