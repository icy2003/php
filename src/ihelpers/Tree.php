<?php
/**
 * Class Tree
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

use icy2003\php\I;

/**
 * 树操作
 */
class Tree
{
    /**
     * 展开一棵树
     *
     * - 展开后为一维数组，并且靠后的元素的 pid 对应 id 的元素一定在靠前的元素里存在
     *
     * @param array $array
     * @param string $rootId 根节点 ID
     * @param string $idName ID 名
     * @param string $pidName PID 名
     *
     * @return array
     */
    public static function expand($array, $rootId = '0', $idName = 'id', $pidName = 'pid')
    {
        $array = Arrays::indexBy($array, $idName);
        $array2 = Arrays::indexBy($array, $pidName, true);
        $pidArray = [$rootId];
        $return = [];
        while (true) {
            $temp = [];
            foreach ($pidArray as $pid) {
                $rows = I::get($array2, $pid, []);
                if (empty($rows)) {
                    continue;
                }
                $return = Arrays::merge($return, $rows);
                $temp = Arrays::merge($temp, Arrays::column($rows, $idName));
            }
            if (empty($temp)) {
                break;
            }
            $pidArray = $temp;
        }
        return $return;
    }
}
