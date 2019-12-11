<?php

namespace icy2003\php\iextensions\zetacomponents;

use ezcMailRfc2231Implementation as GlobalEzcMailRfc2231Implementation;
use ezcMailContentDispositionHeader;

class ezcMailRfc2231Implementation extends GlobalEzcMailRfc2231Implementation{

    public static function parseContentDisposition( $header, ezcMailContentDispositionHeader $cd = null )
    {
        if ( $cd === null )
        {
            $cd = new ezcMailContentDispositionHeader();
        }

        $parsedHeader = self::parseHeader( $header );
        $cd->disposition = $parsedHeader[0];
        if ( isset( $parsedHeader[1] ) )
        {
            foreach ( $parsedHeader[1] as $paramName => $data )
            {
                switch ( $paramName )
                {
                    case 'filename':
                        $cd->fileName = $data['value'];
                        $cd->displayFileName = trim( $data['value'], '"' );
                        if ( isset( $data['charset'] ) )
                        {
                            $cd->fileNameCharSet = $data['charset'];
                            $cd->displayFileName = ezcMailCharsetConverter::convertToUTF8Iconv( $cd->displayFileName, $cd->fileNameCharSet );
                        }
                        // Work around for bogus email clients that think
                        // it's allowed to use mime-encoding for filenames.
                        // It isn't, see RFC 2184, and issue #13038.
                        else if ( preg_match( '@^=\?[^?]+\?[QqBb]\?@', $cd->displayFileName ) )
                        {
                            $cd->displayFileName = ezcMailTools::mimeDecode( $cd->displayFileName );
                        }

                        if ( isset( $data['language'] ) )
                        {
                            $cd->fileNameLanguage = $data['language'];
                        }
                        break;
                    case 'creation-date':
                        $cd->creationDate = $data['value'];
                        break;
                    case 'modification-date':
                        $cd->modificationDate = $data['value'];
                        break;
                    case 'read-date':
                        $cd->readDate = $data['value'];
                        break;
                    case 'size':
                        $cd->size = $data['value'];
                        break;
                    default:
                        $cd->additionalParameters[$paramName] = $data['value'];
                        if ( isset( $data['charset'] ) )
                        {
                            $cd->additionalParametersMetaData[$paramName]['charSet'] = $data['charset'];
                        }
                        if ( isset( $data['language'] ) )
                        {
                            $cd->additionalParametersMetaData[$paramName]['language'] = $data['language'];
                        }
                        break;
                }
            }
        }
        return $cd;
    }
}