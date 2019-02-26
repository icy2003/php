<?php

namespace icy2003\iexts\phpword;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor as T;

class TemplateProcessor extends T
{

    public function __construct($documentTemplate)
    {
        parent::__construct($documentTemplate);
        $this->_tempDocumentSettingPart = $this->readPartWithRels($this->_getSettingName());
    }

    protected function _getSettingName()
    {
        return 'word/settings.xml';
    }

    protected $_tempDocumentSettingPart = "";

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

    public function getSettings()
    {
        return $this->_tempDocumentSettingPart;
    }

    /**
     * 在主文档中搜索
     *
     * @param string $search 待搜索的关键字
     *
     * @return integer 关键字位置
     */
    public function tagPos($search)
    {
        $search = parent::ensureMacroCompleted($search);
        return strpos($this->tempDocumentMainPart, $search);
    }

    /**
     * 覆盖父类的 save 方法
     *
     * @return string
     */
    public function save()
    {
        foreach ($this->tempDocumentHeaders as $index => $xml) {
            $this->savePartWithRels($this->getHeaderName($index), $xml);
        }

        $this->savePartWithRels($this->getMainPartName(), $this->tempDocumentMainPart);
        $this->savePartWithRels($this->_getSettingName(), $this->_tempDocumentSettingPart);

        foreach ($this->tempDocumentFooters as $index => $xml) {
            $this->savePartWithRels($this->getFooterName($index), $xml);
        }

        $this->zipClass->addFromString($this->getDocumentContentTypesName(), $this->tempDocumentContentTypes);

        // Close zip file
        if (false === $this->zipClass->close()) {
            throw new Exception('Could not close zip file.'); // @codeCoverageIgnore
        }

        return $this->tempDocumentFilename;
    }

    private function __getBodyBlock($string)
    {
        if (preg_match('%(?i)(?<=<w:body>)[\s|\S]*?(?=</w:body>)%', $string, $matches)) {
            return $matches[0];
        } else {
            return '';
        }
    }

    /**
     * 将一个 word 模板变量替换成表格
     * @see https://github.com/PHPOffice/PHPWord/issues/1198 感谢提供思路
     *
     * @param string $var 变量名
     * @param array $array 二维数组
     * @param boolean $useCellRef 是否使用单元格引用（例如A3），暂时没用
     *                  格式参考 phpspreadsheet 的 rangeToArray 或者 icy2003/iexts/phpspreadsheet/Worksheet/iWorksheet rangeToArray
     * @todo 给表格加样式
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

    /**
     * 将一个 word 模板变量替换成列表
     *
     * @param string $var 变量名，如 `list`，在 word 里应该写：${list}${/list}
     * @param array $array 一维数组
     * @todo 给列表加样式
     *
     * @return void
     */
    public function setList($var, $array)
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        foreach ($array as $item) {
            $section->addListItem($item, 0);
        }
        $objWriter = IOFactory::createWriter($phpWord);
        $xml = $objWriter->getWriterPart('Document')->write();
        $this->replaceBlock($var, $this->__getBodyBlock($xml));
    }

    /**
     * 是否强制提示更新字段（用于更新目录）
     *
     * @param boolean $isUpdate
     *
     * @return void
     */
    public function setIsUpdateFields($isUpdate = true)
    {
        $string = $isUpdate ? 'true' : 'false';
        $matches = null;
        if (preg_match('/<w:updateFields w:val=\"(true|false)\"\/>/', $this->_tempDocumentSettingPart, $matches)) {
            $this->_tempDocumentSettingPart = str_replace($matches[0], '<w:updateFields w:val="' . $string . '"/>', $this->_tempDocumentSettingPart);
        } else {
            $this->_tempDocumentSettingPart = str_replace('</w:settings>', '<w:updateFields w:val="' . $string . '"/></w:settings>', $this->_tempDocumentSettingPart);
        }
    }

}
