<?php

namespace icy2003\php\iexts\phpspreadsheet\Worksheet;

use icy2003\php\ihelpers\Env;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class iWorksheet
{
    /**
     * 返回一个指定范围内的单元格的数组
     * 修复不被支持的函数： NUMBERSTRING
     * ps：作者表示不修车
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $workSheet
     * @param string $pRange 单元格范围 (i.e. "A1:B10"), 或者一个单元格 (i.e. "A1")
     * @param mixed $nullValue 单元格内容不存在时返回的值
     * @param bool $calculateFormulas 是否计算公式的值？
     * @param bool $formatData 是否应用格式化到该数据
     * @param bool $returnCellRef false - 按照索引返回数组，true - 按照真实的行列返回数组
     * @param array $params 额外条件参数
     *                  onlyVisible 是否只返回可见单元格，默认 true
     *                  fillColor 是否按填充颜色返回，如："#ff0000"，表示只返回红色背景的数据，默认 false，表示不限制
     *
     *
     * @return array
     */
    public static function rangeToArray($workSheet, $pRange, $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false, $params = [])
    {
        $returnValue = [];
        list($rangeStart, $rangeEnd) = Coordinate::rangeBoundaries($pRange);
        $minCol = Coordinate::stringFromColumnIndex($rangeStart[0]);
        $minRow = $rangeStart[1];
        $maxCol = Coordinate::stringFromColumnIndex($rangeEnd[0]);
        $maxRow = $rangeEnd[1];

        ++$maxCol;
        $r = -1;
        $onlyVisible = Env::value($params, 'onlyVisible', true);
        $fillColor = Env::value($params, 'fillColor', false);
        for ($row = $minRow; $row <= $maxRow; ++$row) {
            if (true === $onlyVisible) {
                $rowVisible = $workSheet->getRowDimension($row)->getVisible();
                if (!$rowVisible) {
                    continue;
                }
            }
            $rRef = ($returnCellRef) ? $row : ++$r;
            $c = -1;
            for ($col = $minCol; $col != $maxCol; ++$col) {
                if (true === $onlyVisible) {
                    $columnVisible = $workSheet->getColumnDimension($col)->getVisible();
                    if (!$columnVisible) {
                        continue;
                    }
                }
                $cRef = ($returnCellRef) ? $col : ++$c;
                if ($fillColor) {
                    if ($cellStyle->getFill()->getStartColor()->getARGB() != 'FF' . substr(strtoupper($fillColor), 1)) {
                        continue;
                    }
                }
                if ($workSheet->getCellCollection()->has($col . $row)) {
                    $cell = $workSheet->getCellCollection()->get($col . $row);
                    if ($cell->getValue() !== null) {
                        if ($cell->getValue() instanceof RichText) {
                            $returnValue[$rRef][$cRef] = $cell->getValue()->getPlainText();
                        } else {
                            if ($calculateFormulas) {
                                // fix
                                if (false !== strpos(strtoupper($cell->getValue()), "NUMBERSTRING")) {
                                    $returnValue[$rRef][$cRef] = $cell->getOldCalculatedValue();
                                } else {
                                    $returnValue[$rRef][$cRef] = $cell->getCalculatedValue();
                                }
                            } else {
                                $returnValue[$rRef][$cRef] = $cell->getValue();
                            }
                        }

                        if ($formatData) {
                            $style = $workSheet->getParent()->getCellXfByIndex($cell->getXfIndex());
                            $returnValue[$rRef][$cRef] = NumberFormat::toFormattedString(
                                $returnValue[$rRef][$cRef],
                                ($style && $style->getNumberFormat()) ? $style->getNumberFormat()->getFormatCode() : NumberFormat::FORMAT_GENERAL
                            );
                        }
                    } else {
                        $returnValue[$rRef][$cRef] = $nullValue;
                    }
                } else {
                    $returnValue[$rRef][$cRef] = $nullValue;
                }
            }
        }

        return $returnValue;
    }
}
