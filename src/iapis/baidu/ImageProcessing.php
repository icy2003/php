<?php
/**
 * Class ImageProcessing
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\iapis\baidu;

use icy2003\php\I;
use icy2003\php\ihelpers\Arrays;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;

/**
 * 图像处理
 */
class ImageProcessing extends Base
{
    /**
     * 选项列表
     *
     * @var array
     */
    protected $_options = [
        'image' => null,
    ];

    /**
     * 设置选项
     *
     * @param array $options
     * - image：Base64编码字符串，以图片文件形式请求时必填。(支持图片格式：jpg，bmp，png，jpeg)，图片大小不超过4M。长宽乘积不超过800p x 800px。注意：图片的base64编码是不包含图片头的，如（data:image/jpg;base64,）
     *
     * @return static
     */
    public function setOptions($options)
    {
        return parent::setOptions($options);
    }

    /**
     * 图像无损放大
     *
     * - 输入一张图片，可以在尽量保持图像质量的条件下，将图像在长宽方向各放大两倍
     *
     * @return static
     */
    public function qualityEnchance()
    {
        $this->requestToken();
        $this->_result = Json::decode(Http::post('https://aip.baidubce.com/rest/2.0/image-process/v1/image_quality_enhance', Arrays::some($this->_options, [
            'image',
        ]), [
            'access_token' => $this->_token,
        ]));
        $this->_toArrayCall = function($result) {
            return I::get($result, 'image');
        };

        return $this;
    }
}
