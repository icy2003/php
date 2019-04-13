<?php

namespace icy2003\php\iexts\PhpOffice\PhpSpreadsheet\Worksheet;

use icy2003\php\I;
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
     * @param bool $returnCellRef false - 按照索引返回数组，true - 按照真实的行列返回数组
     * @param bool $calculateFormulas 是否计算公式的值？
     * @param bool $formatData 是否应用格式化到该数据
     * @param array $params 额外条件参数
     *                  onlyVisible 是否只返回可见单元格，默认 true
     *                  fillColor 是否按填充颜色返回，如："#ff0000"，表示只返回红色背景的数据，默认 false，表示不限制
     *
     *
     * @return array
     */
    public static function rangeToArray($workSheet, $pRange, $nullValue = null, $returnCellRef = true, $calculateFormulas = true, $formatData = true, $params = [])
    {
        $returnValue = [];
        $styleArray = [];
        list($rangeStart, $rangeEnd) = Coordinate::rangeBoundaries($pRange);
        $minCol = Coordinate::stringFromColumnIndex($rangeStart[0]);
        $minRow = $rangeStart[1];
        $maxCol = Coordinate::stringFromColumnIndex($rangeEnd[0]);
        $maxRow = $rangeEnd[1];

        ++$maxCol;
        $r = -1;
        $onlyVisible = I::value($params, 'onlyVisible', true);
        $fillColor = I::value($params, 'fillColor', false);
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
                if ($workSheet->getCellCollection()->has($col . $row)) {
                    $cell = $workSheet->getCellCollection()->get($col . $row);
                    if ($fillColor) {
                        if ($cell->getStyle()->getFill()->getStartColor()->getRGB() != substr(strtoupper($fillColor), 1)) {
                            continue;
                        }
                    }
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
                    // 背景图目前有bug：当背景是白色时，拿到的是黑色，这里可以按文字颜色判断：如果文字不是白色，那黑色背景强制改成白色
                    $bgColor = $cell->getStyle()->getFill()->getStartColor()->getRGB();
                    $color = $cell->getStyle()->getFont()->getColor()->getRGB();
                    if ('FFFFFF' != $color) {
                        if ('000000' == $bgColor) {
                            $bgColor = 'FFFFFF';
                        }
                    }
                    $styleArray[$rRef][$cRef] = [
                        'color' => $color,
                        'name' => $cell->getStyle()->getFont()->getName(),
                        'size' => $cell->getStyle()->getFont()->getSize(),
                        'bold' => $cell->getStyle()->getFont()->getBold(),
                        'italic' => $cell->getStyle()->getFont()->getItalic(),
                        'underline' => $cell->getStyle()->getFont()->getUnderline(),
                        'alignment' => $cell->getStyle()->getAlignment()->getHorizontal(),
                        'valign' => $cell->getStyle()->getAlignment()->getVertical(),
                        'bgColor' => $bgColor,
                    ];
                } else {
                    $returnValue[$rRef][$cRef] = $nullValue;
                    $styleArray[$rRef][$cRef] = [];
                }
            }
        }

        // 过滤空白行列，如果末尾有合并的单元格，因为被合并的单元格本身是空，因此这里会以合并单元格为准，再筛选掉空白行列
        $mergeArray = array_keys($workSheet->getMergeCells());
        if (!empty($mergeArray)) {
            $rowMax = $colMax = 1;
            foreach ($mergeArray as $range) {
                // rangeStart 没用，因为肯定不会比 rangeEnd 大
                list($rangeStart, $rangeEnd) = Coordinate::rangeBoundaries($range);
                $startCol = Coordinate::stringFromColumnIndex($rangeStart[0]);
                $endCol = Coordinate::stringFromColumnIndex($rangeEnd[0]);
                // 隐藏单元格不拿时，可能会因为隐藏了合并单元格的一部分导致数据拿不到，因此需要做检测
                if (true === $onlyVisible) {
                    $s = 0;
                    for ($i = $startCol; $i <= $endCol; $i++) {
                        for ($j = $rangeStart[1]; $j <= $rangeEnd[1]; $j++) {
                            $colVisible = $workSheet->getColumnDimension($i)->getVisible();
                            $rowVisible = $workSheet->getRowDimension($j)->getVisible();
                            if (0 === $s++) {
                                $initVisible = $colVisible && $rowVisible;
                            }
                            $pVisible = $colVisible && $rowVisible;
                            if (true === $initVisible ^ $pVisible) {
                                throw new \Exception("不允许隐藏合并单元格的一部分，该合并单元格范围是{$range}，其中{$i}{$j}等单元格被隐藏了");
                            }
                        }
                    }
                }
                if ($rangeEnd[0] > $colMax) {
                    $colMax = $rangeEnd[0];
                }
                if ($rangeEnd[1] > $rowMax) {
                    $rowMax = $rangeEnd[1];
                }
            }
        }
        // 去掉空白行和列
        $data = [];
        $style = [];
        foreach ($returnValue as $r => $row) {
            // 如果使用索引，那么索引和真实行差1
            if (false === $returnCellRef) {
                $rowIndex = $r + 1;
            } else {
                $rowIndex = $r;
            }
            if (!empty(array_filter($row)) || !empty($mergeArray) && $rowIndex <= $rowMax) {
                foreach ($row as $c => $value) {
                    // 如果使用索引，那么索引和真实列差1
                    if (false === $returnCellRef) {
                        $colIndex = $c + 1;
                    } else {
                        $colIndex = Coordinate::columnIndexFromString($c);
                    }
                    $isEmpty = true;
                    foreach ($returnValue as $d) {
                        // 如果内容不为空，或者在合并单元格中出现，这个单元格不能算成是空
                        if (!empty($d[$c]) || !empty($mergeArray) && $colIndex <= $colMax) {
                            $isEmpty = false;
                            break;
                        }
                    }
                    if (false === $isEmpty) {
                        // 使用之前的下标
                        $data[$r][$c] = $value;
                        $style[$r][$c] = $styleArray[$r][$c];
                    }
                }
            }
        }

        return [$data, $style];
    }
}
