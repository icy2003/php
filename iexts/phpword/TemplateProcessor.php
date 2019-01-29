<?php

namespace icy2003\iexts\phpword;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor as T;

class TemplateProcessor extends T
{
    public function getMain()
    {
        return $this->tempDocumentMainPart;
    }

    public function setMain($main)
    {
        $this->tempDocumentMainPart = $main;
        return $this;
    }

    public function getHeaders()
    {
        return $this->tempDocumentHeaders;
    }

    public function getFooters()
    {
        return $this->tempDocumentFooters;
    }

    public function tagPos($search)
    {
        return strpos($this->tempDocumentMainPart, $search);
    }

    /**
     * 将一个 word 模板变量替换成表格
     * @see https://github.com/PHPOffice/PHPWord/issues/1198 感谢提供思路
     *
     * @param string $var 变量名
     * @param array $array 二维数组
     * @param boolean $useCellRef 是否使用单元格引用（例如A3），暂时没用
     *                  格式参考 phpspreadsheet 的 rangeToArray 或者 icy2003/iexts/phpspreadsheet/Worksheet/iWorksheet rangeToArray
     *
     * @return void
     */
    public function setTable($var, $array, $useCellRef = true)
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $style = $phpWord->addTableStyle('tableStyle', [
            'borderColor' => '000000',
            'borderSize' => 9,
            'cellMarginLeft' => 150,
            'cellMarginRight' => 150,
        ]);
        $table = $section->addTable($style);
        foreach ($array as $r => $row) {
            // echo $r;
            $table->addRow();
            foreach ($row as $c => $value) {
                // echo $c;
                $table->addCell()->addText($value);
            }
        }
        $objWriter = IOFactory::createWriter($phpWord);
        $xml = $objWriter->getWriterPart('Document')->write();
        $this->replaceBlock($var, $this->__getBodyBlock($xml));
    }

    private function __getBodyBlock($string)
    {
        if (preg_match('%(?i)(?<=<w:body>)[\s|\S]*?(?=</w:body>)%', $string, $matches)) {
            return $matches[0];
        } else {
            return '';
        }
    }
}
