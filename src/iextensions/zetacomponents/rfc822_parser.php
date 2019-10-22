<?php

namespace icy2003\php\iextensions\zetacomponents;

use ezcMailRfc822Parser as iEzcMailRfc822Parser;

class ezcMailRfc822Parser extends iEzcMailRfc822Parser
{

    /**
     * @var int
     */
    private $parserState = parent::PARSE_STATE_HEADERS;

    /**
     * @var ezcMailHeadersHolder
     */
    private $headers = null;

    /**
     * @var ezcMailPartParser
     */
    private $bodyParser = null;

    public function parseBody($origLine)
    {
        $line = rtrim($origLine, "\r\n");
        if ($this->parserState == parent::PARSE_STATE_HEADERS && $line == '') {
            $this->parserState = parent::PARSE_STATE_BODY;

            $headers = new ezcMailHeadersHolder();
            $headers['Content-Type'] = $this->headers['Content-Type'];
            if (isset($this->headers['Content-Transfer-Encoding'])) {
                $headers['Content-Transfer-Encoding'] = $this->headers['Content-Transfer-Encoding'];
            }

            if (isset($this->headers['Content-Disposition'])) {
                $headers['Content-Disposition'] = $this->headers['Content-Disposition'];
            }

            $this->bodyParser = parent::createPartParserForHeaders($headers);
        } else if ($this->parserState == parent::PARSE_STATE_HEADERS) {
            $this->parseHeader($line, $this->headers);
        } else // we are parsing headers
        {
            $this->bodyParser->parseBody($origLine);
        }
    }
}
