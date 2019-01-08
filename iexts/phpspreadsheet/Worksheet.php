<?php

namespace icy2003\iexts\phpspreadsheet;

use icy2003\ihelpers\Env;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Worksheet
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
     * @param bool $onlyVisible 是否只返回可见单元格
     * @param array $params 额外条件参数
     *                  fillColor 填充颜色，如："#ff0000"，表示只返回红色背景的数据
     *
     *
     * @return array
     */
    public static function rangeToArray($workSheet, $pRange, $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false, $onlyVisible = false, $params = [])
    {
        $returnValue = [];
        list($rangeStart, $rangeEnd) = Coordinate::rangeBoundaries($pRange);
        $minCol = Coordinate::stringFromColumnIndex($rangeStart[0]);
        $minRow = $rangeStart[1];
        $maxCol = Coordinate::stringFromColumnIndex($rangeEnd[0]);
        $maxRow = $rangeEnd[1];

        ++$maxCol;
        $r = -1;
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
                if (!empty($params)) {
                    $cellStyle = $workSheet->getStyle($col . $row);
                    if (array_key_exists('fillColor', $params)) {
                        if ($cellStyle->getFill()->getStartColor()->getARGB() != 'FF' . substr(strtoupper(Env::value($params, 'fillColor')), 1)) {
                            continue;
                        }
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

    /**
     * 返回一个指定范围内的可见的单元格的数组
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $workSheet
     * @param string $pRange 单元格范围 (i.e. "A1:B10"), 或者一个单元格 (i.e. "A1")
     * @param bool $onlyVisible 是否只选择可见，true 是 false 否
     * @param mixed $nullValue 单元格内容不存在时返回的值
     * @param bool $calculateFormulas 是否计算公式的值？
     * @param bool $formatData 是否应用格式化到该数据
     * @param bool $returnCellRef false - 按照索引返回数组，true - 按照真实的行列返回数组
     *
     * @return array
     */
    public static function rangeToVisibleArray($workSheet, $pRange, $onlyVisible = true, $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false)
    {
        return static::rangeToArray($workSheet, $pRange, $nullValue, $calculateFormulas, $formatData, $returnCellRef, $onlyVisible);
    }

    /**
     * 返回一个指定范围内的单元格的数组
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $workSheet
     * @param string $pRange 单元格范围 (i.e. "A1:B10"), 或者一个单元格 (i.e. "A1")
     * @param array $params 额外条件参数
     *                  onlyVisible 是否只返回可见单元格，默认“是”
     *                  fillColor 填充颜色，如："#ff0000"，表示只返回红色背景的数据
     * @param mixed $nullValue 单元格内容不存在时返回的值
     * @param bool $calculateFormulas 是否计算公式的值？
     * @param bool $formatData 是否应用格式化到该数据
     * @param bool $returnCellRef false - 按照索引返回数组，true - 按照真实的行列返回数组
     *
     * @return array
     */
    public static function rangeToArrayByParams($workSheet, $pRange, $params, $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false)
    {
        $onlyVisible = Env::value($params, 'onlyVisible', true);
        unset($params['onlyVisible']);
        return static::rangeToArray($workSheet, $pRange, $nullValue, $calculateFormulas, $formatData, $returnCellRef, $onlyVisible, $params);
    }
}
