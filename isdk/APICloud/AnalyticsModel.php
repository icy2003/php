<?php

namespace icy2003\isdk\APICloud;

use icy2003\ihelpers\Json;

/**
 * 统计云 API.
 *
 * @see https://docs.apicloud.com/Cloud-API/stat-cloud-api
 */
class AnalyticsModel extends ApiCloudModel
{
    // 统计云 API. 地址
    const URL_ANALYTICS = 'https://r.apicloud.com/analytics/';

    // 应用统计信息获取接口
    const METHOD_GETAPPSTATISTICDATABYID = 'getAppStatisticDataById';

    // 应用各版本统计信息获取接口
    const METHOD_GETVERSIONSSTATISTICDATABYID = 'getVersionsStatisticDataById';

    // 应用地理分布统计信息获取接口
    const METHOD_GETGEOSTATISTICDATABYID = 'getGeoStatisticDataById';

    // 应用设备分布统计信息获取接口
    const METHOD_GETDEVICESTATISTICDATABYID = 'getDeviceStatisticDataById';

    // 应用异常错误统计信息获取接口
    const METHOD_GETEXCEPTIONSSTATISTICDATABYID = 'getExceptionsStatisticDataById';

    // 应用异常错误详细信息获取接口
    const METHOD_GETEXCEPTIONSDETAILBYTITLE = 'getExceptionsDetailByTitle';

    /**
     * 应用统计信息获取接口.
     * 该接口主要用于获取用户指定应用ID及时间范围内的相关应用统计数据信息.
     *
     * @param mixed $startDate
     * @param mixed $endDate
     * @param int   $useTimestamp
     *
     * @return string
     */
    public function getAppStatisticDataById($startDate, $endDate = null, $useTimestamp = true)
    {
        if (null == $endDate) {
            $endDate = time();
        }
        if (true === $useTimestamp) {
            $startDate = date('YYYY-MM-DD', $startDate);
            $endDate = date('YYYY-MM-DD', $endDate);
        }
        $post['startDate'] = $startDate;
        $post['endDate'] = $endDate;

        return  $this->post(self::URL_ANALYTICS.self::METHOD_GETAPPSTATISTICDATABYID, Json::encode($post));
    }

    /**
     * 应用各版本统计信息获取接口.
     * 该接口主要用于获取用户指定应用ID及时间范围内相关应用各版本的统计数据信息.
     *
     * @param mixed $startDate
     * @param mixed $endDate
     * @param int   $useTimestamp
     *
     * @return string
     */
    public function getVersionsStatisticDataById($startDate, $endDate = null, $useTimestamp = true)
    {
        if (null == $endDate) {
            $endDate = time();
        }
        if (true === $useTimestamp) {
            $startDate = date('YYYY-MM-DD', $startDate);
            $endDate = date('YYYY-MM-DD', $endDate);
        }
        $post['startDate'] = $startDate;
        $post['endDate'] = $endDate;

        return  $this->post(self::URL_ANALYTICS.self::METHOD_GETVERSIONSSTATISTICDATABYID, Json::encode($post));
    }

    /**
     * 应用地理分布统计信息获取接口
     * 该接口主要用于获取用户指定应用ID及时间范围内的应用下各版本地理分布统计数据信息.
     *
     * @param mixed  $startDate
     * @param mixed  $endDate
     * @param string $versionCode
     * @param int    $useTimestamp
     *
     * @return string
     */
    public function getGeoStatisticDataById($startDate, $endDate = null, $versionCode = '', $useTimestamp = true)
    {
        if (null == $endDate) {
            $endDate = time();
        }
        if (true === $useTimestamp) {
            $startDate = date('YYYY-MM-DD', $startDate);
            $endDate = date('YYYY-MM-DD', $endDate);
        }

        $post['startDate'] = $startDate;
        $post['endDate'] = $endDate;
        $post['versionCode'] = $versionCode;

        return  $this->post(self::URL_ANALYTICS.self::METHOD_GETGEOSTATISTICDATABYID, Json::encode($post));
    }

    /**
     * 应用设备分布统计信息获取接口
     * 该接口主要用于获取用户指定应用ID及时间范围内的应用下各版本设备信息分布统计数据信息.
     *
     * @param mixed $startDate
     * @param mixed $endDate
     * @param int   $useTimestamp
     *
     * @return string
     */
    public function getDeviceStatisticDataById($startDate, $endDate = null, $useTimestamp = true)
    {
        if (null == $endDate) {
            $endDate = time();
        }
        if (true === $useTimestamp) {
            $startDate = date('YYYY-MM-DD', $startDate);
            $endDate = date('YYYY-MM-DD', $endDate);
        }

        $post['startDate'] = $startDate;
        $post['endDate'] = $endDate;

        return  $this->post(self::URL_ANALYTICS.self::METHOD_GETGEOSTATISTICDATABYID, Json::encode($post));
    }

    /**
     * 应用异常错误统计信息获取接口
     * 该接口主要用于获取用户指定应用ID及时间范围内的应用下各版本异常错误统计数据信息.
     *
     * @param mixed $startDate
     * @param mixed $endDate
     * @param int   $useTimestamp
     *
     * @return string
     */
    public function getExceptionsStatisticDataById($startDate, $endDate = null, $useTimestamp = true)
    {
        if (null == $endDate) {
            $endDate = time();
        }
        if (true === $useTimestamp) {
            $startDate = date('YYYY-MM-DD', $startDate);
            $endDate = date('YYYY-MM-DD', $endDate);
        }

        $post['startDate'] = $startDate;
        $post['endDate'] = $endDate;

        return  $this->post(self::URL_ANALYTICS.self::METHOD_GETEXCEPTIONSSTATISTICDATABYID, Json::encode($post));
    }

    /**
     * 应用异常错误详细信息获取接口
     * 该接口主要用于根据应用异常错误摘要获取异常错误详细信息.
     *
     * @param string $title
     *
     * @return string
     */
    public function getExceptionsDetailByTitle($title)
    {
        $post['title'] = $title;

        return  $this->post(self::URL_ANALYTICS.self::METHOD_GETEXCEPTIONSDETAILBYTITLE, Json::encode($post));
    }
}
