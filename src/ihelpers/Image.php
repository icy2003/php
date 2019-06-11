<?php
/**
 * Class Image
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

use icy2003\php\I;

/**
 * 图片处理类
 */
class Image
{
    /**
     * 单例对象
     *
     * @var static
     */
    protected static $_instance;

    /**
     * 构造函数
     *
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * 克隆函数
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * 输入图片对象
     *
     * @var resource
     */
    protected $_imageIn;

    /**
     * 输出图片对象
     *
     * @var resource
     */
    protected $_imageOut;

    /**
     * 图片属性
     *
     * @var array
     */
    protected $_attributes = [];

    /**
     * 创建单例
     *
     * @param string $image 图片路径
     *
     * @return static
     */
    public static function create($image)
    {
        if (!static::$_instance instanceof static ) {
            static::$_instance = new static();
            $attributes = static::$_instance->__parseImage($image);
            array_map(function ($value, $key) {
                static::$_instance->_attributes[$key] = $value;
            }, array_values($attributes), array_keys($attributes));
            static::$_instance->_imageIn = static::$_instance->_attributes['object'];
        }
        return static::$_instance;
    }

    /**
     * 解析图片属性
     *
     * @param string $image 图片地址
     *
     * @return array
     */
    private function __parseImage($image)
    {
        if (false === ($size = @getimagesize($image))) {
            throw new \Exception('不是有效的图片：' . $image);
        }
        // static::$_instance->_attributes['size'] = $size;
        $width = $size[0];
        $height = $size[1];
        $mime = $size['mime'];
        switch ($size[2]) {
            case 1:
                $ext = 'gif';
                $object = imagecreatefromgif($image);
                $method = 'imagegif';
                break;
            case 2:
                $ext = 'jpg';
                $object = imagecreatefromjpeg($image);
                $method = 'imagejpeg';
                break;
            case 3:
                $ext = 'png';
                $object = imagecreatefrompng($image);
                $method = 'imagepng';
                break;
        }
        return [
            'width' => $width,
            'height' => $height,
            'mime' => $mime,
            'ext' => $ext,
            'object' => $object,
            'method' => $method,
        ];
    }

    /**
     * 获取图片属性数组
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * 获取图片属性
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getAttribute($name)
    {
        return I::get($this->_attributes, $name);
    }

    /**
     * 设置透明度
     *
     * @return void
     */
    private function __setTransparency()
    {
        $index = imagecolortransparent($this->_imageIn);
        $color = ['red' => 255, 'green' => 255, 'blue' => 255];
        if ($index >= 0) {
            $color = imagecolorsforindex($this->_imageIn, $index);
        }
        $index = imagecolorallocate($this->_imageOut, $color['red'], $color['green'], $color['blue']);
        imagefill($this->_imageOut, 0, 0, $index);
        imagecolortransparent($this->_imageOut, $index);
    }

    /**
     * 缩放图片
     *
     * @param array|int $zoom 数组或数字，如：数组[1, 0.5]表示宽不变，高变成一半，如果是整数，表示等比例缩放
     *
     * @return static
     */
    public function zoom($zoom = 1)
    {
        if (is_array($zoom) && 2 === count($zoom)) {
            list($zoomWidth, $zoomHeight) = $zoom;
            $zoomWidth *= $this->_attributes['width'];
            $zoomHeight *= $this->_attributes['height'];
        } else {
            $zoom = (int)$zoom;
            $zoomWidth = $zoom * $this->_attributes['width'];
            $zoomHeight = $zoom * $this->_attributes['height'];
        }
        $this->_imageOut = imagecreatetruecolor($zoomWidth, $zoomHeight);
        $this->__setTransparency();
        imagecopyresampled($this->_imageOut, $this->_imageIn, 0, 0, 0, 0, $zoomWidth, $zoomHeight, $this->_attributes['width'], $this->_attributes['height']);

        return $this;
    }

    /**
     * 裁剪
     *
     * @param array $cut 裁剪的宽高，如：[100, 200]
     * @param array $pos 裁剪起始 x 和 y 坐标，坐标原点在左上角，如：[0, 0]
     *
     * @return static
     */
    public function cut($cut = null, $pos = [0, 0])
    {
        null === $cut && $cut = [$this->_attributes['width'], $this->_attributes['height']];
        $width = min($this->_attributes['width'], $cut[0]);
        $height = min($this->_attributes['height'], $cut[1]);
        $x = min($this->_attributes['width'], $pos[0]);
        $y = min($this->_attributes['height'], $pos[1]);
        $this->_imageOut = imagecreatetruecolor($width, $height);
        $this->__setTransparency();
        imagecopy($this->_imageOut, $this->_imageIn, 0, 0, $x, $y, $this->_attributes['width'], $this->_attributes['height']);

        return $this;
    }

    /**
     * 创建文字水印
     *
     * @param string $text 文字水印内容
     * @param array $pos 水印位置，默认在左上角的坐标原点
     * @param mixed $fontColor 颜色值，支持类型参见 Color::create
     * @param int $fontSize 字体大小，默认 12
     * @param string $fontPath 字体路径，默认宋体
     *
     * @return static
     */
    public function markText($text, $pos = [0, 0], $fontColor = 'black', $fontSize = 12, $fontPath = 'simkai')
    {
        $this->_imageOut = imagecreatetruecolor($this->_attributes['width'], $this->_attributes['height']);
        $this->__setTransparency();
        $text = Charset::toUtf($text);
        $temp = imagettfbbox($fontSize, 0, $fontPath, $text);
        $textWidth = $temp[2] - $temp[6];
        // $textHeight = $temp[3] - $temp[7];
        imagesettile($this->_imageOut, $this->_imageIn);
        imagefilledrectangle($this->_imageOut, 0, 0, $this->_attributes['width'], $this->_attributes['height'], IMG_COLOR_TILED);
        list($red, $green, $blue) = Color::create($fontColor)->toRGB();
        $text2 = imagecolorallocate($this->_imageOut, $red, $green, $blue);
        $posX = min($pos[0], $this->_attributes['width'] - $textWidth);
        $posY = min($pos[1], $this->_attributes['height']);
        imagettftext($this->_imageOut, $fontSize, 0, $posX, $posY, $text2, $fontPath, $text);

        return $this;
    }

    /**
     * 创建图片水印
     *
     * @param string $image 图片水印的地址
     * @param array $pos 水印位置，默认在左上角的坐标原点
     * @param array $size 图片水印大小，如果不给则是默认大小
     *
     * @return static
     */
    public function markPicture($image, $pos = [0, 0], $size = null)
    {
        $this->_imageOut = imagecreatetruecolor($this->_attributes['width'], $this->_attributes['height']);
        $this->__setTransparency();
        $markAttrs = $this->__parseImage($image);
        null === $size && $size = [$markAttrs['width'], $markAttrs['height']];
        imagecopy($this->_imageOut, $this->_imageIn, 0, 0, 0, 0, $this->_attributes['width'], $this->_attributes['height']);
        $posX = min($pos[0], $this->_attributes['width'] - $size[0]);
        $posY = min($pos[1], $this->_attributes['height'] - $size[1]);
        imagecopyresized($this->_imageOut, $markAttrs['object'], $posX, $posY, 0, 0, $size[0], $size[1], $markAttrs['width'], $markAttrs['height']);
        imagedestroy($markAttrs['object']);
        return $this;
    }

    /**
     * 沿着 Y 轴翻转
     *
     * @return static
     */
    public function turnY()
    {
        $this->_imageOut = imagecreatetruecolor($this->_attributes['width'], $this->_attributes['height']);
        for ($x = 0; $x < $this->_attributes['width']; $x++) {
            imagecopy($this->_imageOut, $this->_imageIn, $this->_attributes['width'] - $x - 1, 0, $x, 0, 1, $this->_attributes['height']);
        }
        return $this;
    }

    /**
     * 沿着 X 轴翻转
     *
     * @return static
     */
    public function turnX()
    {
        $this->_imageOut = imagecreatetruecolor($this->_attributes['width'], $this->_attributes['height']);
        for ($y = 0; $y < $this->_attributes['height']; $y++) {
            imagecopy($this->_imageOut, $this->_imageIn, 0, $this->_attributes['height'] - $y - 1, 0, $y, $this->_attributes['width'], 1);
        }
        return $this;
    }

    /**
     * 保存图片到某个路径下
     *
     * @param string $path 目标路径
     *
     * @return void
     */
    public function saveTo($path = './')
    {
        $method = $this->_attributes['method'];
        $this->_attributes['out'] = $path . date('YmdHis') . '.' . $this->_attributes['ext'];
        $method($this->_imageOut, $this->_attributes['out']);
    }

    /**
     * 显示图片到浏览器
     *
     * @return void
     */
    public function show()
    {
        header('Content-type:' . $this->_attributes['mime']);
        $method = $this->_attributes['method'];
        $method($this->_imageOut);
    }

    /**
     * 释放图片资源
     *
     * @return void
     */
    public function destroy()
    {
        imagedestroy($this->_imageIn);
        imagedestroy($this->_imageOut);
    }

    /**
     * 生成验证码
     *
     * @todo 智能处理文字和图片间距大小等
     *
     * @param string $code 验证码
     * @param array $size 验证码图片宽高，默认 80*30
     * @param int $fontSize 字体大小，默认 14
     * @param string $fontPath 字体路径，默认宋体
     * @param int $pixelNum 杂点数量，默认 2
     * @param int $pixelColor 杂点颜色数量，默认 5
     * @param int $padding 文字左右间距
     * @param int $margin 文字左边距
     * @param int $base 文字上边距
     * @param int $baseOffset 文字抖动偏差
     */
    public static function captcha($code, $size = [80, 30], $fontSize = 14, $fontPath = 'simkai', $pixelNum = 2, $pixelColor = 5, $padding = 8, $margin = 7, $base = 20, $baseOffset = 4)
    {
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Pragma: no-cache");
        header("Cache-control: private");
        header('Content-Type: image/png');
        $codeLength = Strings::length($code);
        $image = imagecreatetruecolor($size[0], $size[1]);
        imagefilledrectangle($image, 0, 0, $size[0] - 1, $size[1] - 1, imagecolorallocate($image, mt_rand(235, 255), mt_rand(235, 255), mt_rand(235, 255)));
        for ($i = 0; $i < $pixelColor; $i++) {
            $noiseColor = imagecolorallocate($image, mt_rand(150, 225), mt_rand(150, 225), mt_rand(150, 225));
            for ($j = 0; $j < $pixelNum; $j++) {
                imagestring($image, 1, mt_rand(-10, $size[0]), mt_rand(-10, $size[1]), Strings::random(1), $noiseColor);
            }
        }
        $codeArray = Strings::strSplit($code);
        for ($i = 0; $i < $codeLength; ++$i) {
            $color = imagecolorallocate($image, mt_rand(0, 100), mt_rand(20, 120), mt_rand(50, 150));
            imagettftext($image, $fontSize, mt_rand(-10, 10), $margin, mt_rand($base - $baseOffset, $base + $baseOffset), $color, $fontPath, $codeArray[$i]);
            $margin += (imagefontwidth($fontSize) + $padding);
        }
        imagepng($image);
        imagedestroy($image);
    }
}
