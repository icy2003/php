<?php
/**
 * Class Image
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

use Exception;
use icy2003\php\C;
use icy2003\php\I;

/**
 * 图片处理类
 */
class Image
{

    /**
     * 加载图片
     *
     * @param string $image 图片路径
     *
     * @return void
     */
    public function __construct($image)
    {
        $this->_attributes = $this->__parseImage($image);
        $this->_imageIn = $this->_attributes['object'];
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
     * 解析图片属性
     *
     * @param string $image 图片地址
     *
     * @return array
     */
    private function __parseImage($image)
    {
        $image = I::getAlias($image);
        C::assertTrue(is_array($size = @getimagesize($image)), '不是有效的图片：' . $image);
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
            default:
                throw new Exception("不支持的图片类型");
        }
        return [
            'file' => $image,
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
        null === $this->_imageOut && $this->_imageOut = imagecreatetruecolor($this->_attributes['width'], $this->_attributes['height']);
        $index = imagecolorallocate($this->_imageOut, $color['red'], $color['green'], $color['blue']);
        imagefill($this->_imageOut, 0, 0, $index);
        imagecolortransparent($this->_imageOut, $index);
    }

    /**
     * 缩放图片
     *
     * @param array|integer $zoom 数组或数字，如：数组[1, 0.5]表示宽不变，高变成一半，如果是整数，表示等比例缩放
     *
     * @return static
     */
    public function zoom($zoom = 1)
    {
        if (2 === Arrays::count($zoom)) {
            list($zoomWidth, $zoomHeight) = $zoom;
            $zoomWidth *= $this->_attributes['width'];
            $zoomHeight *= $this->_attributes['height'];
        } else {
            $zoom = (int) $zoom;
            $zoomWidth = $zoom * $this->_attributes['width'];
            $zoomHeight = $zoom * $this->_attributes['height'];
        }
        if ($this->_imageOut = imagecreatetruecolor($zoomWidth, $zoomHeight)) {
            $this->__setTransparency();
            imagecopyresampled($this->_imageOut, $this->_imageIn, 0, 0, 0, 0, $zoomWidth, $zoomHeight, $this->_attributes['width'], $this->_attributes['height']);
        }

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
        if ($this->_imageOut = imagecreatetruecolor($width, $height)) {
            $this->__setTransparency();
            imagecopy($this->_imageOut, $this->_imageIn, 0, 0, $x, $y, $this->_attributes['width'], $this->_attributes['height']);
        }

        return $this;
    }

    /**
     * 创建文字水印
     *
     * @param string $text 文字水印内容
     * @param array $pos 水印位置，默认在左上角的坐标原点
     * @param mixed $fontColor 颜色值，支持类型参见 Color
     * @param int $fontSize 字体大小，默认 12
     * @param string $fontPath 字体路径，默认宋体
     *
     * @return static
     */
    public function markText($text, $pos = [0, 0], $fontColor = 'black', $fontSize = 12, $fontPath = 'simkai')
    {
        if ($this->_imageOut = imagecreatetruecolor($this->_attributes['width'], $this->_attributes['height'])) {
            $this->__setTransparency();
            $text = Charset::toUtf($text);
            $temp = imagettfbbox($fontSize, 0, $fontPath, $text);
            $textWidth = $temp[2] - $temp[6];
            // $textHeight = $temp[3] - $temp[7];
            imagesettile($this->_imageOut, $this->_imageIn);
            imagefilledrectangle($this->_imageOut, 0, 0, $this->_attributes['width'], $this->_attributes['height'], IMG_COLOR_TILED);
            list($red, $green, $blue) = (new Color($fontColor))->toRGB()->get();
            $text2 = imagecolorallocate($this->_imageOut, $red, $green, $blue);
            $posX = min($pos[0], $this->_attributes['width'] - $textWidth);
            $posY = min($pos[1], $this->_attributes['height']);
            imagettftext($this->_imageOut, $fontSize, 0, $posX, $posY, $text2, $fontPath, $text);
        }

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
        if ($this->_imageOut = imagecreatetruecolor($this->_attributes['width'], $this->_attributes['height'])) {
            $this->__setTransparency();
            $markAttrs = $this->__parseImage($image);
            null === $size && $size = [$markAttrs['width'], $markAttrs['height']];
            imagecopy($this->_imageOut, $this->_imageIn, 0, 0, 0, 0, $this->_attributes['width'], $this->_attributes['height']);
            $posX = min($pos[0], $this->_attributes['width'] - $size[0]);
            $posY = min($pos[1], $this->_attributes['height'] - $size[1]);
            imagecopyresized($this->_imageOut, /** @scrutinizer ignore-type */$markAttrs['object'], $posX, $posY, 0, 0, $size[0], $size[1], $markAttrs['width'], $markAttrs['height']);
            imagedestroy(/** @scrutinizer ignore-type */$markAttrs['object']);
        }

        return $this;
    }

    /**
     * 沿着 Y 轴翻转
     *
     * @return static
     */
    public function turnY()
    {
        if ($this->_imageOut = imagecreatetruecolor($this->_attributes['width'], $this->_attributes['height'])) {
            for ($x = 0; $x < $this->_attributes['width']; $x++) {
                imagecopy($this->_imageOut, $this->_imageIn, $this->_attributes['width'] - $x - 1, 0, $x, 0, 1, $this->_attributes['height']);
            }
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
        if ($this->_imageOut = imagecreatetruecolor($this->_attributes['width'], $this->_attributes['height'])) {
            for ($y = 0; $y < $this->_attributes['height']; $y++) {
                imagecopy($this->_imageOut, $this->_imageIn, 0, $this->_attributes['height'] - $y - 1, 0, $y, $this->_attributes['width'], 1);
            }
        }

        return $this;
    }

    /**
     * 逆时针旋转图片
     *
     * @param integer $degrees 角度
     *
     * @return static
     */
    public function rotate($degrees = -90)
    {
        $this->_imageOut = imagerotate($this->_attributes['object'], $degrees, 0);
        $this->__setTransparency();

        return $this;
    }

    /**
     * 获取 (x, y) 坐标处的颜色值
     *
     * @param integer $x
     * @param integer $y
     *
     * @return \icy2003\php\ihelpers\Color Color 类对象
     */
    public function getColor($x, $y){
        $rgb = imagecolorat($this->_attributes['object'], $x, $y);
        return new Color([($rgb >> 16) & 0xFF, ($rgb >> 8) & 0xFF, $rgb & 0xFF], Color::TYPE_RGB);
    }

    /**
     * 保存图片到某个路径下，文件名自动生成
     *
     * @param string $path 目标路径
     *
     * @return void
     */
    public function saveTo($path = './')
    {
        $this->save($path . date('YmdHis') . '.' . $this->_attributes['ext']);
    }

    /**
     * 保存到文件，如果不给文件名，则保存回原文件
     *
     * @return void
     */
    public function save($file = null)
    {
        null === $file && $file = $this->_attributes['file'];
        $this->_attributes['out'] = $file;
        $method = $this->_attributes['method'];
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
        null === $this->_imageOut && $this->_imageOut = $this->_imageIn;
        $method($this->_imageOut);
    }

    /**
     * 释放图片资源
     *
     * @return void
     */
    public function destroy()
    {
        is_resource($this->_imageIn) && imagedestroy($this->_imageIn);
        is_resource($this->_imageOut) && imagedestroy($this->_imageOut);
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
     *
     * @return void
     */
    public static function captcha($code, $size = [80, 30], $fontSize = 14, $fontPath = 'simkai', $pixelNum = 2, $pixelColor = 5, $padding = 8, $margin = 7, $base = 20, $baseOffset = 4)
    {
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Pragma: no-cache");
        header("Cache-control: private");
        header('Content-Type: image/png');
        $codeLength = Strings::length($code);
        if ($image = imagecreatetruecolor($size[0], $size[1])) {
            imagefilledrectangle($image, 0, 0, $size[0] - 1, $size[1] - 1, imagecolorallocate($image, mt_rand(235, 255), mt_rand(235, 255), mt_rand(235, 255)));
            for ($i = 0; $i < $pixelColor; $i++) {
                $noiseColor = imagecolorallocate($image, mt_rand(150, 225), mt_rand(150, 225), mt_rand(150, 225));
                for ($j = 0; $j < $pixelNum; $j++) {
                    imagestring($image, 1, mt_rand(-10, $size[0]), mt_rand(-10, $size[1]), Strings::random(1), $noiseColor);
                }
            }
            $codeArray = Strings::split($code);
            for ($i = 0; $i < $codeLength; ++$i) {
                $color = imagecolorallocate($image, mt_rand(0, 100), mt_rand(20, 120), mt_rand(50, 150));
                imagettftext($image, $fontSize, mt_rand(-10, 10), $margin, mt_rand($base - $baseOffset, $base + $baseOffset), $color, $fontPath, $codeArray[$i]);
                $margin += (imagefontwidth($fontSize) + $padding);
            }
            imagepng($image);
            imagedestroy($image);
        }
    }

    /**
     * 释放输入输出图片
     */
    public function __destruct()
    {
        $this->destroy();
    }
}
