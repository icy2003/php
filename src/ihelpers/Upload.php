<?php
/**
 * Class Upload
 *
 * @link https://www.icy2003.com/
 * @author icy2003 <2317216477@qq.com>
 * @copyright Copyright (c) 2017, icy2003
 */
namespace icy2003\php\ihelpers;

use icy2003\php\I;
use icy2003\php\icomponents\file\LocalFile;

/**
 * 文件类上传类
 */
class Upload
{
    /**
     * 单例对象
     *
     * @var static
     */
    protected static $_instance;

    /**
     * 构造函数
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
     * 创建文件上传单例.
     *
     * @param array $config
     *                      formName 文件上传时的表单名，默认 'file'
     *                      sizeLimit 文件上传大小限制，默认 0，不限制
     *                      extLimit 文件类型限制，默认 []，不限制
     *
     * @return static
     */
    public static function create($config = [])
    {
        if (!static::$_instance instanceof static ) {
            static::$_instance = new static();
            static::$_instance->__formName = I::get($config, 'formName', 'file');
            static::$_instance->__sizeLimit = static::$_instance->__getSizeLimit(I::get($config, 'sizeLimit', 0));
            static::$_instance->__extLimit = I::get($config, 'extLimit', []);
        }

        return static::$_instance;
    }

    /**
     * 下载一个文件
     *
     * @param string $fileName
     *
     * @return
     */
    public static function download($fileName)
    {
        try {
            $local = new LocalFile();
            if ($local->isFile($fileName)) {
                header('Content-type:application/octet-stream');
                header('Accept-Ranges:bytes');
                header('Accept-Length:' . $local->getFilesize($fileName));
                header('Content-Disposition: attachment; filename=' . Charset::toCn($local->getBasename($fileName)));
                foreach ($local->dataGenerator($fileName) as $data) {
                    echo $data;
                }
            }
        } catch (\Exception $e) {
            header('HTTP/1.1 404 Not Found');
            echo $e->getMessage();
        }
    }

    /**
     * 成功
     */
    const ERROR_SUCCESS = 0;
    /**
     * 超出 upload_max_filesize 选项限制
     */
    const ERROR_UPLOAD_MAX_FILESIZE = 1;
    /**
     * 超出表单中 MAX_FILE_SIZE 选项的值
     */
    const ERROR_MAX_FILE_SIZE = 2;
    /**
     * 文件只有部分被上传
     */
    const ERROR_PART_UPLOAD = 3;
    /**
     * 没有文件被上传
     */
    const ERROR_FILE_NOT_FOUND = 4;
    /**
     * 找不到临时文件夹
     */
    const ERROR_TEMP_DIR_NOT_FOUND = 6;
    /**
     * 文件写入失败
     */
    const ERROR_WRITE_FAILED = 7;
    /**
     * 文件扩展没有打开
     */
    const ERROR_EXT_CLOSE = 8;
    /**
     * 文件保存失败
     */
    const ERROR_SAVE_FAILED = -1;
    /**
     * 超出文件大小限制
     */
    const ERROR_SIZE_LIMIT = -2;
    /**
     * 不允许的文件类型
     */
    const ERROR_EXT_LIMIT = -3;

    /**
     * 没有文件字段
     */
    const ERROR_NO_FORM_FIELD = -4;

    /**
     * 错误信息列表
     *
     * @var array
     */
    private static $__errorMap = [
        self::ERROR_SUCCESS => '文件上传成功',
        self::ERROR_UPLOAD_MAX_FILESIZE => '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值',
        self::ERROR_MAX_FILE_SIZE => '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
        self::ERROR_PART_UPLOAD => '文件只有部分被上传',
        self::ERROR_FILE_NOT_FOUND => '没有文件被上传',
        self::ERROR_TEMP_DIR_NOT_FOUND => '找不到临时文件夹',
        self::ERROR_WRITE_FAILED => '文件写入失败',
        self::ERROR_EXT_CLOSE => ' php 文件上传扩展 file 没有打开',
        self::ERROR_SAVE_FAILED => '文件保存失败',
        self::ERROR_SIZE_LIMIT => '超出自定义的文件上传大小限制',
        self::ERROR_EXT_LIMIT => '不允许的文件类型',
        self::ERROR_NO_FORM_FIELD => '没有指定文件表单字段',
    ];

    /**
     * 属性列表
     *
     * @var array
     */
    private $__attributes = [];
    /**
     * 默认的上传表单字段名
     *
     * @var string
     */
    private $__formName = 'file';
    /**
     * 默认上传限制
     *
     * @var integer
     */
    private $__sizeLimit = 0;
    /**
     * 默认扩展限制
     *
     * @var array
     */
    private $__extLimit = [];
    /**
     * 错误代码
     *
     * @var integer
     */
    private $__errorCode = 0;

    /**
     * 文件上传，对上传的文件进行处理，需要用 save()、saveTo()、saveAs() 保存.
     *
     * @return static
     */
    public function upload()
    {
        if (false === isset($_FILES[$this->__formName])) {
            $this->__errorCode = self::ERROR_NO_FORM_FIELD;
            return $this;
        }
        if (self::ERROR_SUCCESS === $_FILES[$this->__formName]['error']) {
            if (is_uploaded_file($file = $_FILES[$this->__formName]['tmp_name'])) {
                $localFile = new LocalFile();
                $fileName = $_FILES[$this->__formName]['name'];
                $fileSize = $localFile->getFilesize($file);
                $fileExt = $localFile->getExtension($fileName);
                if ($fileSize > $this->__sizeLimit) {
                    $this->__errorCode = self::ERROR_SIZE_LIMIT;

                    return $this;
                }
                if (!empty($this->__extLimit) && !in_array($fileExt, $this->__extLimit)) {
                    $this->__errorCode = self::ERROR_EXT_LIMIT;

                    return $this;
                }
                $this->__attributes['md5'] = md5_file($file);
                $this->__attributes['sha1'] = sha1_file($file);
                $this->__attributes['ext'] = $fileExt;
                $this->__attributes['size'] = $fileSize;
                $this->__attributes['filectime'] = $localFile->splInfo($file)->getCTime();
                $this->__attributes['filemtime'] = $localFile->splInfo($file)->getMTime();
                $this->__attributes['fileatime'] = $localFile->splInfo($file)->getATime();
                $this->__attributes['originName'] = $fileName;
                $this->__attributes['fileName'] = date('YmdHis') . Strings::random(10) . '.' . $fileExt;
                $this->__errorCode = self::ERROR_SUCCESS;

                return $this;
            } else {
                $this->__errorCode = self::ERROR_SAVE_FAILED;

                return $this;
            }
        } else {
            // 其他错误时的处理
            $this->__errorCode = $_FILES[$this->__formName]['error'];

            return $this;
        }
    }

    /**
     * 保存文件至目录.
     *
     * @param string $dirPath 目录
     * @param string $fileName 文件名，如果不给则用系统随机的文件名
     *
     * @return boolean
     */
    public function saveTo($dirPath, $fileName = null)
    {
        $localFile = new LocalFile();
        $localFile->createDir($dirPath);
        null === $fileName && $fileName = $this->__attributes['fileName'];
        return move_uploaded_file($_FILES[$this->__formName]['tmp_name'], rtrim($dirPath, '/') . '/' . $fileName);
    }

    /**
     * 保存文件至路径或目录
     * - saveAs()：$path 为文件时
     * - saveTo()：$path 为目录时
     */
    public function save($path, $fileName = null)
    {
        $localFile = new LocalFile();
        if ($localFile->isFile($path)) {
            return $this->saveAs($path);
        } else {
            return $this->saveTo($path, $fileName);
        }
    }

    /**
     * 保存文件至指定路径.
     * - 注意：如果目标文件已存在但只是文件名相同，实际是不同的文件，则会修改 fileName 属性并且保存为新的文件
     *
     * @param string $filePath 文件路径
     *
     * @return boolean
     */
    public function saveAs($filePath)
    {
        $localFile = new LocalFile();
        $dirName = $localFile->getDirname($filePath);
        $localFile->createDir($dirName);
        $fileName = $localFile->getFilename($filePath);
        if ($localFile->isFile($filePath)) {
            if (md5_file($filePath) === $this->__attributes['md5'] && sha1_file($filePath) === $this->__attributes['sha1']) {
                return true;
            } else {
                $this->__attributes['fileName'] = $localFile->getBasename($fileName) . '_' . time() . '.' . $localFile->getExtension($fileName);
                return $this->saveAs($dirName . '/' . $this->__attributes['fileName']);
            }
        }
        return move_uploaded_file($_FILES[$this->__formName]['tmp_name'], $filePath);
    }

    /**
     * 文件上传的错误码
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->__errorCode;
    }

    /**
     * 文件上传是否成功
     *
     * @return boolean
     */
    public function success()
    {
        return self::ERROR_SUCCESS === $this->__errorCode;
    }

    /**
     * 文件上传的错误信息.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return self::$__errorMap[$this->__errorCode];
    }

    /**
     * 返回上传后文件的属性.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->__attributes;
    }

    /**
     * 结合系统限制，找出文件大小限制
     *
     * @param string $configLimit
     *
     * @return string
     */
    private function __getSizeLimit($configLimit)
    {
        $array = [
            Numbers::toBytes(I::phpini('upload_max_filesize', 0)),
            Numbers::toBytes(I::phpini('post_max_size', 0)),
            Numbers::toBytes($configLimit),
        ];
        return min(array_filter($array));
    }
}
