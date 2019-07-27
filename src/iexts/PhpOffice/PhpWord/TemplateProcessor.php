<?php
/**
 * Class TemplateProcessor
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */

namespace icy2003\php\iexts\PhpOffice\PhpWord;

use icy2003\php\I;
use icy2003\php\ihelpers\Html;
use icy2003\php\ihelpers\Regular;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor as T;

/**
 * TemplateProcessor 扩展
 */
class TemplateProcessor extends T
{

    /**
     * TemplateProcessor 构造器
     *
     * @param string $documentTemplate Word 文件路径
     */
    public function __construct($documentTemplate)
    {
        parent::__construct($documentTemplate);
        $this->_tempDocumentSettingPart = $this->readPartWithRels($this->_getSettingName());
    }

    /**
     * 获取 Word 的设置文件名
     *
     * @return string
     */
    protected function _getSettingName()
    {
        return 'word/settings.xml';
    }

    /**
     * Word 的设置部分
     *
     * @var string
     */
    protected $_tempDocumentSettingPart = '';

    /**
     * Word 的主体部分
     *
     * @return string
     */
    public function getMain()
    {
        return $this->tempDocumentMainPart;
    }

    /**
     * 设置主体代码
     *
     * @param string $main
     *
     * @return static
     */
    public function setMain($main)
    {
        $this->tempDocumentMainPart = $main;
        return $this;
    }

    /**
     * 获取头部
     *
     * @return string
     */
    public function getHeaders()
    {
        return $this->tempDocumentHeaders;
    }

    /**
     * 获取脚部
     *
     * @return string
     */
    public function getFooters()
    {
        return $this->tempDocumentFooters;
    }

    /**
     * 获取设置
     *
     * @return string
     */
    public function getSettings()
    {
        return $this->_tempDocumentSettingPart;
    }

    /**
     * 搜索关键字
     *
     * @param string $search 待搜索的关键字
     * @param string $part 搜索的字符串部分，如果是 null，则搜索整个 main 内容
     *
     * @return integer 关键字位置
     */
    public function tagPos($search, $part = null)
    {
        $search = parent::ensureMacroCompleted($search);
        if (null === $part) {
            return strpos($this->tempDocumentMainPart, $search);
        } else {
            return strpos($part, $search);
        }
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

    /**
     * 获取块
     *
     * @param string $string
     *
     * @return string
     */
    private function __getBodyBlock($string)
    {
        if (preg_match('%(?i)(?<=<w:body>)[\s|\S]*?(?=</w:body>)%', $string, $matches)) {
            return $matches[0];
        } else {
            return '';
        }
    }

    /**
     * 在某部分 XML 串中，将一个 Word 模板变量替换成表格，并合并到主文档上
     *
     * @param string $documentPartXML 某段 XML 字串
     * @param string $var 变量名
     * @param array $array 只支持行列号的二维数组
     * @param array $mergeArray 合并单元格的数组，例如：['A1:B1', 'C1:C2']
     * @param array $styleArray 对应所有单元格的样式二维数组
     * @param integer $count 引用返回被替换的次数
     *
     * @return boolean
     */
    public function setTableFromPart($documentPartXML, $var, $array, $mergeArray = [], $styleArray = [], &$count = null)
    {
        if ($this->tagPos($var, $documentPartXML)) {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            /**
             * addTableStyle 有 bug……
             * @see https://github.com/PHPOffice/PHPWord/issues/629
             */
            $table = $section->addTable([
                'unit' => TblWidth::PERCENT,
                'width' => 100 * 50,
                'borderColor' => '000000',
                'borderSize' => 9,
                'cellMarginLeft' => 150,
                'cellMarginRight' => 150,
            ]);
            $cindexs = [];
            foreach ($array as $rowIndex => $row) {
                // echo $rowIndex; 1
                $table->addRow();
                $i = 0;
                if (empty($cindexs)) {
                    $cindexs = array_map(function ($cs) {
                        return Coordinate::columnIndexFromString($cs);
                    }, array_keys($row));
                }
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
                                $i = count(array_intersect(range($rangeStart[0], $rangeEnd[0]), $cindexs)) - 1;
                                $array = array_merge($array, ['gridSpan' => $i + 1]);
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
                        'valign' => I::get($styleArray, $rowIndex . '.' . $c . '.valign', Jc::CENTER),
                        'bgColor' => I::get($styleArray, $rowIndex . '.' . $c . '.bgColor'),
                    ]);
                    $alignment = I::get($styleArray, $rowIndex . '.' . $c . '.alignment', Jc::CENTER);
                    // 在高版本的 Word 里，不支持 JC::JUSTIFY 呢
                    if (JC::JUSTIFY == $alignment) {
                        $alignment = JC::BOTH;
                    }
                    $cellStyle = I::get($styleArray, $rowIndex . '.' . $c);
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
            $this->replaceBlock($var, $this->__getBodyBlock($xml), $documentPartXML, $count);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 在整个文档里，将一个 Word 模板变量替换成表格
     *
     * @see https://github.com/PHPOffice/PHPWord/issues/1198 感谢提供思路
     *
     * @param string $var 变量名
     * @param array $array 只支持行列号的二维数组
     * @param array $mergeArray 合并单元格的数组，例如：['A1:B1', 'C1:C2']
     * @param array $styleArray 对应所有单元格的样式二维数组
     * @param integer $count 引用返回被替换的次数
     *
     * @return boolean
     */
    public function setTable($var, $array, $mergeArray = [], $styleArray = [], &$count = null)
    {
        return $this->setTableFromPart($this->tempDocumentMainPart, $var, $array, $mergeArray, $styleArray, $count);
    }

    /**
     * 在某部分 XML 串中，将一个 Word 模板变量替换成列表，并合并到主文档上
     *
     * @param string $documentPartXML 某段 XML 字串
     * @param string $var 变量名，如 `list`，在 word 里应该写：${list}${/list}
     * @param array $array 一维数组
     * @param integer $depth 列表层级，从 0 开始。默认 0
     * @param integer $count 引用返回被替换的次数
     *
     * @return boolean
     */
    public function setListFromPart($documentPartXML, $var, $array, $depth = 0, &$count = null)
    {
        if ($this->tagPos($var, $documentPartXML)) {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            foreach ($array as $item) {
                $section->addListItem($item, $depth);
            }
            $objWriter = IOFactory::createWriter($phpWord);
            $xml = $objWriter->getWriterPart('Document')->write();
            $this->replaceBlock($var, $this->__getBodyBlock($xml), $documentPartXML, $count);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 在整个文档里，将一个 Word 模板变量替换成列表
     *
     * @param string $var 变量名，如 `list`，在 word 里应该写：${list}${/list}
     * @param array $array 一维数组
     * @param integer $depth 列表层级，从 0 开始。默认 0
     * @param integer $count 引用返回被替换的次数
     *
     * @todo 给列表加样式
     *
     * @return boolean
     */
    public function setList($var, $array, $depth = 0, &$count = null)
    {
        return $this->setListFromPart($this->tempDocumentMainPart, $var, $array, $depth, $count);
    }

    /**
     * 是否强制提示更新字段（用于更新目录）
     *
     * @param boolean $isUpdate
     *
     * @return boolean
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
            return true;
        } else {
            return false;
        }
    }

    /**
     * 替换块标签
     * 注：原方法因为贪婪模式可能无法正确匹配对应的块
     *
     * @param string $blockname 变量名
     * @param string $replacement 替换字符串
     * @param string $documentPartXML xml 字符串，默认整个文档的
     * @param integer $count 引用返回被替换的次数
     *
     * @return void
     */
    public function replaceBlock($blockname, $replacement, $documentPartXML = null, &$count = null)
    {
        null === $documentPartXML && $documentPartXML = $this->tempDocumentMainPart;
        // PHP7.0~7.2 会有 bug 导致匹配不到结果，例子参见 samples/php7preg_bug.php
        Regular::jitOff();
        preg_match(
            '/(<w:p ((?!<w:p ).)*?\${' . $blockname . '}.*?<\/w:p>)(.*?)(<w:p ((?!<w:p ).)*\${\/' . $blockname . '}.*?<\/w:p>)/is',
            $documentPartXML,
            $matches
        );
        if (isset($matches[1])) {
            $part = $this->setValueForPart($matches[1] . $matches[3] . $matches[4], $replacement, $documentPartXML, parent::MAXIMUM_REPLACEMENTS_DEFAULT, $count);
            $this->tempDocumentMainPart = $this->setValueForPart($documentPartXML, $part, $this->tempDocumentMainPart, parent::MAXIMUM_REPLACEMENTS_DEFAULT);
        }
    }

    /**
     * 在某段 XML 中替换变量，并合并到主文档上
     *
     * @param string $documentPartXML 某段 XML 字串
     * @param string $search 待搜索的变量名
     * @param string $replace 替换的值
     * @param integer $limit 替换次数，默认-1，表示替换全部
     * @param integer $count 引用返回被替换的次数
     *
     * @return boolean
     */
    public function setValueFromPart($documentPartXML, $search, $replace, $limit = parent::MAXIMUM_REPLACEMENTS_DEFAULT, &$count = null)
    {
        if ($this->tagPos($search, $documentPartXML)) {
            $part = $this->setValueForPart(static::ensureMacroCompleted($search), static::ensureUtf8Encoded($replace), $documentPartXML, $limit, $count);
            $this->tempDocumentMainPart = $this->setValueForPart($documentPartXML, $part, $this->tempDocumentMainPart, $limit);
            $this->setValue($search, $replace, $limit);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @see parent::setValueForPart
     *
     * - 引用返回替换次数
     *
     * @param string $search
     * @param string $replace
     * @param string $documentPartXML
     * @param integer $limit
     * @param integer $count
     *
     * @return string
     */
    protected function setValueForPart($search, $replace, $documentPartXML, $limit, &$count = null)
    {
        // Note: we can't use the same function for both cases here, because of performance considerations.
        if (self::MAXIMUM_REPLACEMENTS_DEFAULT === $limit) {
            return str_replace($search, $replace, $documentPartXML, $count);
        }
        $regExpEscaper = new RegExp();

        return preg_replace($regExpEscaper->escape($search), $replace, $documentPartXML, $limit, $count);
    }

}
