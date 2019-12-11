<?php

namespace icy2003\php\iextensions\zetacomponents;

use ezcMailHeadersHolder;
use ezcMailRfc822Parser as GlobalEzcMailRfc822Parser;

class ezcMailRfc822Parser extends GlobalEzcMailRfc822Parser
{
    public function __construct()
    {
        $this->headers = new ezcMailHeadersHolder();
    }

    /**
     * @var int
     */
    private $parserState = parent::PARSE_STATE_HEADERS;

    /**
     * @var \ezcMailHeadersHolder
     */
    private $headers = null;

    /**
     * @var \ezcMailPartParser
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
            $this->bodyParser = ezcMailPartParser::createPartParserForHeaders($headers);
        } else if ($this->parserState == parent::PARSE_STATE_HEADERS) {
            $this->parseHeader($line, $this->headers);
        } else // we are parsing headers
        {
            $this->bodyParser->parseBody($origLine);
        }
    }

    /**
     * @var string
     */
    private $lastParsedHeader = null;

    protected function parseHeader($line, \ezcMailHeadersHolder $headers)
    {
        $matches = array();
        preg_match_all("/^([\w\-_]*):\s?(.*)/", $line, $matches, PREG_SET_ORDER);
        if (count($matches) > 0) {
            if (!in_array(strtolower($matches[0][1]), (array) parent::$uniqueHeaders)) {
                $arr = $headers[$matches[0][1]];
                $arr[0][] = str_replace("\t", " ", trim($matches[0][2]));
                $headers[$matches[0][1]] = $arr;
            } else {
                $headers[$matches[0][1]] = str_replace("\t", " ", trim($matches[0][2]));
            }
            $this->lastParsedHeader = $matches[0][1];
        } else if ($this->lastParsedHeader !== null) {
            if (!in_array(strtolower($this->lastParsedHeader), (array) parent::$uniqueHeaders)) {
                $arr = $headers[$this->lastParsedHeader];
                $arr[0][count($arr[0]) - 1] .= str_replace("\t", " ", $line);
                $headers[$this->lastParsedHeader] = $arr;
            } else {
                $headers[$this->lastParsedHeader] .= str_replace("\t", " ", $line);
            }
        }
    }

    public function finish($class = "ezcMail")
    {
        $mail = new $class();
        $mail->setHeaders($this->headers->getCaseSensitiveArray());
        ezcMailPartParser::parsePartHeaders($this->headers, $mail);

        // from
        if (isset($this->headers['From'])) {
            $mail->from = ezcMailTools::parseEmailAddress($this->headers['From']);
        }
        // to
        if (isset($this->headers['To'])) {
            $mail->to = ezcMailTools::parseEmailAddresses($this->headers['To']);
        }
        // cc
        if (isset($this->headers['Cc'])) {
            $mail->cc = ezcMailTools::parseEmailAddresses($this->headers['Cc']);
        }
        // bcc
        if (isset($this->headers['Bcc'])) {
            $mail->bcc = ezcMailTools::parseEmailAddresses($this->headers['Bcc']);
        }
        // subject
        if (isset($this->headers['Subject'])) {
            $mail->subject = ezcMailTools::mimeDecode($this->headers['Subject']);
            $mail->subjectCharset = 'utf-8';
        }
        // message ID
        if (isset($this->headers['Message-Id'])) {
            $mail->messageID = $this->headers['Message-Id'];
        }

        // Return-Path
        if (isset($this->headers['Return-Path'])) {
            $mail->returnPath = ezcMailTools::parseEmailAddress($this->headers['Return-Path']);
        }

        if ($this->bodyParser !== null) {
            $mail->body = $this->bodyParser->finish();
        }
        return $mail;
    }
}
