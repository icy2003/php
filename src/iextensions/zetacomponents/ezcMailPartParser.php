<?php

namespace icy2003\php\iextensions\zetacomponents;

use ezcMailPartParser as GlobalezcMailPartParser;

abstract class  ezcMailPartParser extends GlobalezcMailPartParser
{

    abstract public function parseBody( $line );

    abstract public function finish();

    protected function parseHeader($line, \ezcMailHeadersHolder $headers)
    {
        $matches = array();
        preg_match_all("/^([\w-_]*):\s?(.*)/", $line, $matches, PREG_SET_ORDER);
        if (count($matches) > 0) {
            if (!in_array(strtolower($matches[0][1]), (array)parent::$uniqueHeaders)) {
                $arr = $headers[$matches[0][1]];
                $arr[0][] = str_replace("\t", " ", trim($matches[0][2]));
                $headers[$matches[0][1]] = $arr;
            } else {
                $headers[$matches[0][1]] = str_replace("\t", " ", trim($matches[0][2]));
            }
            $this->lastParsedHeader = $matches[0][1];
        } else if ($this->lastParsedHeader !== null) // take care of folding
        {
            if (!in_array(strtolower($this->lastParsedHeader), (array)parent::$uniqueHeaders)) {
                $arr = $headers[$this->lastParsedHeader];
                $arr[0][count($arr[0]) - 1] .= str_replace("\t", " ", $line);
                $headers[$this->lastParsedHeader] = $arr;
            } else {
                $headers[$this->lastParsedHeader] .= str_replace("\t", " ", $line);
            }
        }
        // else -invalid syntax, this should never happen.
    }

    public static function parsePartHeaders( \ezcMailHeadersHolder $headers, \ezcMailPart $part )
    {
        if ( isset( $headers['Content-Disposition'] ) )
        {
            $part->contentDisposition = ezcMailRfc2231Implementation::parseContentDisposition( $headers['Content-Disposition'] );
        }
    }

    public static function createPartParserForHeaders( \ezcMailHeadersHolder $headers )
    {
        // default as specified by RFC2045 - #5.2
        $mainType = 'text';
        $subType = 'plain';

        // parse the Content-Type header
        if ( isset( $headers['Content-Type'] ) )
        {
            $matches = array();
            // matches "type/subtype; blahblahblah"
            preg_match_all( '/^(\S+)\/([^;]+)/',
                            $headers['Content-Type'], $matches, PREG_SET_ORDER );
            if ( count( $matches ) > 0 )
            {
                $mainType = strtolower( $matches[0][1] );
                $subType = strtolower( $matches[0][2] );
            }
        }
        $bodyParser = null;

        // create the correct type parser for this the detected type of part
        switch ( $mainType )
        {
            /* RFC 2045 defined types */
            case 'image':
            case 'audio':
            case 'video':
            case 'application':
                $bodyParser = new \ezcMailFileParser( $mainType, $subType, $headers );
                break;

            case 'message':
                switch ( $subType )
                {
                    case "rfc822":
                        $bodyParser = new \ezcMailRfc822DigestParser( $headers );
                        break;

                    case "delivery-status":
                        $bodyParser = new \ezcMailDeliveryStatusParser( $headers );
                        break;

                    default:
                        $bodyParser = new \ezcMailFileParser( $mainType, $subType, $headers );
                        break;
                }
                break;

            case 'text':
                if ( ezcMailPartParser::$parseTextAttachmentsAsFiles === true )
                {
                    $bodyParser = new \ezcMailFileParser( $mainType, $subType, $headers );
                }
                else
                {
                    $bodyParser = new ezcMailTextParser( $subType, $headers );
                }
                break;

            case 'multipart':
                switch ( $subType )
                {
                    case 'mixed':
                        $bodyParser = new \ezcMailMultipartMixedParser( $headers );
                        break;
                    case 'alternative':
                        $bodyParser = new \ezcMailMultipartAlternativeParser( $headers );
                        break;
                    case 'related':
                        $bodyParser = new \ezcMailMultipartRelatedParser( $headers );
                        break;
                    case 'digest':
                        $bodyParser = new \ezcMailMultipartDigestParser( $headers );
                        break;
                    case 'report':
                        $bodyParser = new \ezcMailMultipartReportParser( $headers );
                        break;
                    default:
                        $bodyParser = new \ezcMailMultipartMixedParser( $headers );
                        break;
                }
                break;

                /* extensions */
            default:
                // we treat the body as binary if no main content type is set
                // or if it is unknown
                $bodyParser = new \ezcMailFileParser( $mainType, $subType, $headers );
                break;
        }
        return $bodyParser;
    }
}
