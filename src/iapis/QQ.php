<?php
/**
 * Class QQ
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2020, icy2003
 */
namespace icy2003\php\iapis;

use icy2003\php\C;
use icy2003\php\I;
use icy2003\php\ihelpers\Charset;
use icy2003\php\ihelpers\Http;
use icy2003\php\ihelpers\Json;
use icy2003\php\ihelpers\Strings;

/**
 * QQ 相关接口
 */
class QQ extends Api
{
    /**
     * QQ
     *
     * @var string
     */
    protected $_qq;

    /**
     * 初始化
     *
     * @param string $qq
     */
    public function __construct($qq)
    {
        C::assertTrue(preg_match('|^[1-9]\d{4,10}$|i', $qq) > 0, 'QQ 号格式错误');
        $this->_qq = $qq;
    }

    /**
     * 获取 QQ 的昵称和头像地址
     *
     * @param array $config 可设置头像规格，默认 3
     * - 1~5 分别表示 `40*40`, `40*40`, `100*100`, `140*140`, `640*640`
     *
     * @return static
     */
    public function fetchInfo($config = ['spec'=>3])
    {
        $result = Http::get('http://users.qzone.qq.com/fcg-bin/cgi_get_portrait.fcg', [
            'uins' => $this->_qq,
        ]);
        if (Strings::isContains($result, 'portraitCallBack')) {
            $json = Json::decode(Strings::partBetween($result, 'portraitCallBack(', ')'));
            $this->_result['nickname'] = Charset::toUtf(I::get($json, $this->_qq . '.6'), 'GBK');
        }
        $this->_result['portrait'] = 'https://q2.qlogo.cn/headimg_dl?dst_uin='.$this->_qq.'&spec='. I::get($config, 'spec', 3);

        return $this;
    }
}