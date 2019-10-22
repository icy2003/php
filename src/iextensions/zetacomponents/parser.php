<?php

namespace icy2003\php\iextensions\zetacomponents;

use ezcMailParser as iEzcMailParser;
use ezcMailParserSet;

class ezcMailParser extends iEzcMailParser
{

    /**
     * @var ezcMailPartParser
     */
    private $partParser = null;

    public function parseMail(ezcMailParserSet $set, $class = null)
    {
        $mail = [];
        if (!$set->hasData()) {
            return $mail;
        }
        if (null === $class) {
            $class = $this->options->mailClass;
        }
        do {
            $this->partParser = new ezcMailRfc822Parser();
            $data = '';
            $size = 0;
            while (null !== ($data = $set->getNextLine())) {
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
