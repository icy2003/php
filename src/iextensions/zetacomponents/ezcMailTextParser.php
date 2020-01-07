<?php

namespace icy2003\php\iextensions\zetacomponents;

use ezcMailTextParser as GlobalEzcMailTextParser;
use ezcMail;
use ezcMailText;

class ezcMailTextParser extends GlobalEzcMailTextParser
{
    private $text = null;

    private $headers = null;

    private $subType = null;


    public function __construct( $subType, \ezcMailHeadersHolder $headers )
    {
        $this->subType = $subType;
        $this->headers = $headers;
    }

    public function parseBody( $line )
    {
        $line = rtrim( $line, "\r\n" );
        if ( $this->text === null )
        {
            $this->text = $line;
        }
        else
        {
            $this->text .= "\n" . $line;
        }
    }

    public function finish()
    {
        $charset = "us-ascii"; // RFC 2822 default
        if (isset($this->headers['Content-Type'])) {
            preg_match('/\s*charset\s?=\s?"?([^;"\s]*);?/i',
                $this->headers['Content-Type'],
                $parameters);
            if (count($parameters) > 0) {
                $charset = strtolower(trim($parameters[1], '"'));
            }
        }

        $encoding = strtolower($this->headers['Content-Transfer-Encoding']);
        if ($encoding == ezcMail::QUOTED_PRINTABLE) {
            $this->text = quoted_printable_decode($this->text);
        } else if ($encoding == ezcMail::BASE64) {
            $this->text = base64_decode($this->text);
        }

        $this->text = ezcMailCharsetConverter::convertToUTF8Iconv($this->text, $charset);

        $part = new ezcMailText($this->text, 'utf-8', ezcMail::EIGHT_BIT, $charset);
        $part->subType = $this->subType;
        $part->setHeaders($this->headers->getCaseSensitiveArray());
        ezcMailPartParser::parsePartHeaders($this->headers, $part);
        $part->size = strlen($this->text);
        return $part;
    }
}
