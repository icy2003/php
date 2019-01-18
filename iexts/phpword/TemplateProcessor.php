<?php

namespace icy2003\iexts\phpword;

use PhpOffice\PhpWord\TemplateProcessor as T;

class TemplateProcessor extends T
{
    public function getMain()
    {
        return $this->tempDocumentMainPart;
    }

    public function getHeaders()
    {
        return $this->tempDocumentHeaders;
    }

    public function getFooters()
    {
        return $this->tempDocumentFooters;
    }
}
