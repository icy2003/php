<?php

namespace icy2003\isdk\RongCloud;

class MessageModel extends RongCloud
{
    const METHOD_SYSTEM_PUBLISH = 'message/system/publish.json';

    public function systemPublish($fromUserId, $toUserId, $objectName, $content, $pushContent = '')
    {
        $postBody['fromUserId'] = $fromUserId;
        $postBody['toUserId'] = $toUserId;
        $postBody['objectName'] = $objectName;
        $postBody['content'] = $content;
        $postBody['pushContent'] = $pushContent;

        return $this->post(parent::URL_IM.self::METHOD_SYSTEM_PUBLISH, $postBody);
    }
}
