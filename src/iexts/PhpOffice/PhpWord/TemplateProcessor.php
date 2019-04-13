<?php

namespace icy2003\php\iexts\PhpOffice\PhpWord;

use icy2003\php\I;
use icy2003\php\ihelpers\Html;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
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
     * @param array $array 只支持行列号的二维数组
     * @param array $mergeArray 合并单元格的数组，例如：["A1:B1", "C1:C2"]
     * @param array $styleArray 对应所有单元格的样式二维数组
     *
     * @return void
     */
    public function setTable($var, $array, $mergeArray = [], $styleArray = [])
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
        foreach ($array as $rowIndex => $row) {
            // echo $rowIndex; 1
            $table->addRow();
            $i = 0;
            foreach ($row as $c => $value) {
                if ($i > 0) {
                    $i--;
                    continue;
                }
                // echo $c; A
                $colIndex = Coordinate::columnIndexFromString($c);
                $array = [];
                $isContinue = false;
                foreach ($mergeArray as $range) {
                    // 1,1 : 2,2
                    list($rangeStart, $rangeEnd) = Coordinate::rangeBoundaries($range); // A1:B2
                    if ($rangeEnd[0] > $rangeStart[0]) {
                        if ($colIndex >= $rangeStart[0] && $colIndex <= $rangeEnd[0] && $rowIndex >= $rangeStart[1] && $rowIndex <= $rangeEnd[1]) {
                            $array = array_merge($array, ['gridSpan' => $rangeEnd[0] - $rangeStart[0] + 1]);
                            $i = $rangeEnd[0] - $rangeStart[0];
                        }
                    }
                    if ($rangeEnd[1] > $rangeStart[1]) {
                        if ($colIndex >= $rangeStart[0] && $colIndex <= $rangeEnd[0]) {
                            if ($rowIndex == $rangeStart[1]) {
                                $array = array_merge($array, ['vMerge' => 'restart']);
                            } elseif ($rowIndex > $rangeStart[1] && $rowIndex <= $rangeEnd[1]) {
                                $isContinue = true;
                                $array = array_merge($array, ['vMerge' => 'continue']);
                            }
                        }
                    }
                    if (!empty($array)) {
                        break;
                    }
                }
                $array = array_merge($array, [
                    'valign' => I::value($styleArray, "{$rowIndex}.{$c}.valign", Jc::CENTER),
                    'bgColor' => I::value($styleArray, "{$rowIndex}.{$c}.bgColor"),
                ]);
                $alignment = I::value($styleArray, "{$rowIndex}.{$c}.alignment", Jc::CENTER);
                // 在高版本的 Word 里，不支持 JC::JUSTIFY 呢
                if (JC::JUSTIFY == $alignment) {
                    $alignment = JC::BOTH;
                }
                $cellStyle = I::value($styleArray, "{$rowIndex}.{$c}");
                if (true === $isContinue) {
                    $isContinue = false;
                    $table->addCell(null, $array);
                } else {
                    $table->addCell(null, $array)->addText(Html::encode($value), $cellStyle, [
                        'alignment' => $alignment,
                    ]);
                }
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
        if (preg_match('/w:val=\"TOC\"/', $this->tempDocumentMainPart)) {
            $string = $isUpdate ? 'true' : 'false';
            $matches = null;
            if (preg_match('/<w:updateFields w:val=\"(true|false)\"\/>/', $this->_tempDocumentSettingPart, $matches)) {
                $this->_tempDocumentSettingPart = str_replace($matches[0], '<w:updateFields w:val="' . $string . '"/>', $this->_tempDocumentSettingPart);
            } else {
                $this->_tempDocumentSettingPart = str_replace('</w:settings>', '<w:updateFields w:val="' . $string . '"/></w:settings>', $this->_tempDocumentSettingPart);
            }
        }
    }

    /**
     * 替换块标签
     * 注：原方法因为贪婪模式可能无法正确匹配对应的块
     *
     * @param string $blockname 变量名
     * @param string $replacement 替换字符串
     *
     * @return void
     */
    public function replaceBlock($blockname, $replacement)
    {
        preg_match(
            '/(<\?xml.*?)(<w:p((?!<w:p[ |>]).)*?>\${' . $blockname . '}<\/w:.*?p>)(.*?)(<w:p((?!<w:p[ |>]).)*?\${\/' . $blockname . '}<\/w:.*?p>)/is',
            $this->tempDocumentMainPart,
            $matches
        );

        if (isset($matches[2])) {
            $this->tempDocumentMainPart = str_replace(
                $matches[2] . $matches[4] . $matches[5],
                $replacement,
                $this->tempDocumentMainPart
            );
        }
    }

}
