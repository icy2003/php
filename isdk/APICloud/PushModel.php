<?php

namespace icy2003\isdk\APICloud;

use icy2003\ihelpers\Json;

/**
 * 推送云 API.
 *
 * @see https://docs.apicloud.com/Cloud-API/push-cloud-api
 */
class PushModel extends ApiCloudModel
{
    // 推送云 API 地址
    const URL_PUSH = 'https://p.apicloud.com/api/push/';

    // 消息推送接口
    const METHOD_MESSAGE = 'message';

    public function message($message = [])
    {
        $post['title'] = !empty($message['title']) ? $message['title'] : '消息标题';
        $post['content'] = !empty($message['content']) ? $message['content'] : '消息内容';
        $post['type'] = !empty($message['type']) ? $message['type'] : 1;
        $post['platform'] = !empty($message['platform']) ? $message['platform'] : 0;
        $post['groupName'] = !empty($message['groupName']) ? $message['groupName'] : '';
        $post['userIds'] = !empty($message['userIds']) ? $message['userIds'] : '';

        return  $this->post(self::URL_PUSH.self::METHOD_MESSAGE, Json::encode($post));
    }
}
