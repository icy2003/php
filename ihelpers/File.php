<?php

namespace icy2003\ihelpers;

/**
 * 文件类：主要处理上传下载.
 * File::download($fileName) 静态方法
 * File::create() 创建一个用于接收上传的单例对象，可用方法：upload、save、success、getAttributes、getErrorCode、getErrorMessage.
 */
class File
{
    private static $instance;

    private function __construct()
    {
    }

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
        if (!self::$instance instanceof self) {
            self::$instance = new self();
            !empty($config['formName']) && self::$instance->formName = $config['formName'];
            !empty($config['sizeLimit']) && self::$instance->sizeLimit = $config['sizeLimit'];
            $systemLimit = self::$instance->getSizeLimit();
            self::$instance->sizeLimit = 0 === self::$instance->sizeLimit ? $systemLimit : min($systemLimit, Convert::size(self::$instance->sizeLimit));
            !empty($config['extLimit']) && self::$instance->extLimit = $config['extLimit'];
        }

        return self::$instance;
    }

    /**
     * 下载一个文件.
     *
     * @param string $fileName
     *
     * @return
     */
    public static function download($fileName)
    {
        try {
            if ($file = FileIO::create($fileName)) {
                header('Content-type:application/octet-stream');
                header('Accept-Ranges:bytes');
                header('Accept-Length:' . $file->getAttribute('fileSize'));
                header('Content-Disposition: attachment; filename=' . Charset::convert2cn(basename($fileName)));
                foreach ($file->data() as $data) {
                    echo $data;
                }
            }
        } catch (\Exception $e) {
            Http::code(404, '找不到页面');
            echo $e->getMessage();
        }
    }

    // 成功
    const ERROR_SUCCESS = 0;
    // 超出 upload_max_filesize 选项限制
    const ERROR_UPLOAD_MAX_FILESIZE = 1;
    // 超出表单中 MAX_FILE_SIZE 选项的值
    const ERROR_MAX_FILE_SIZE = 2;
    // 文件只有部分被上传
    const ERROR_PART_UPLOAD = 3;
    // 没有文件被上传
    const ERROR_FILE_NOT_FOUND = 4;
    // 找不到临时文件夹
    const ERROR_TEMP_DIR_NOT_FOUND = 6;
    // 文件写入失败
    const ERROR_WRITE_FAILED = 7;
    // 文件扩展没有打开
    const ERROR_EXT_CLOSE = 8;
    // 文件保存失败
    const ERROR_I_SAVE_FAILED = -1;
    // 超出文件大小限制
    const ERROR_I_SIZE_LIMIT = -2;
    // 不允许的文件类型
    const ERROR_I_EXT_LIMIT = -3;

    private static $errorMap = [
        self::ERROR_SUCCESS => '文件上传成功',
        self::ERROR_UPLOAD_MAX_FILESIZE => '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值',
        self::ERROR_MAX_FILE_SIZE => '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
        self::ERROR_PART_UPLOAD => '文件只有部分被上传',
        self::ERROR_FILE_NOT_FOUND => '没有文件被上传',
        self::ERROR_TEMP_DIR_NOT_FOUND => '找不到临时文件夹',
        self::ERROR_WRITE_FAILED => '文件写入失败',
        self::ERROR_EXT_CLOSE => ' php 文件上传扩展 file 没有打开',
        self::ERROR_I_SAVE_FAILED => '文件保存失败',
        self::ERROR_I_SIZE_LIMIT => '超出自定义的文件上传大小限制',
        self::ERROR_I_EXT_LIMIT => '不允许的文件类型',
    ];
    private $attributes = [];
    private $formName = 'file';
    private $sizeLimit = 0;
    private $extLimit = [];
    private $errorCode = 0;

    /**
     * 文件上传，对上传的文件进行处理，需要用 save() 保存.
     *
     * @return static
     */
    public function upload()
    {
        if (self::ERROR_SUCCESS === $_FILES[$this->formName]['error']) {
            if (is_uploaded_file($file = $_FILES[$this->formName]['tmp_name'])) {
                $fileName = $_FILES[$this->formName]['name'];
                $fileSize = filesize($file);
                $fileExt = $this->getExt($fileName);
                if ($fileSize > $this->sizeLimit) {
                    $this->errorCode = self::ERROR_I_SIZE_LIMIT;

                    return $this;
                }
                if (!empty($this->extLimit) && !in_array($fileExt, $this->extLimit)) {
                    $this->errorCode = self::ERROR_I_EXT_LIMIT;

                    return $this;
                }
                $this->attributes['md5'] = md5_file($file);
                $this->attributes['sha1'] = sha1_file($file);
                $this->attributes['ext'] = $fileExt;
                $this->attributes['size'] = $fileSize;
                $this->attributes['filectime'] = filectime($file);
                $this->attributes['filemtime'] = filemtime($file);
                $this->attributes['fileatime'] = fileatime($file);
                $this->attributes['originName'] = $fileName;
                $this->attributes['fileName'] = $this->randomFileName($fileName);
                $this->errorCode = self::ERROR_SUCCESS;

                return $this;
            } else {
                $this->errorCode = self::ERROR_I_SAVE_FAILED;

                return $this;
            }
        } else {
            // 其他错误时的处理
            $this->errorCode = $_FILES[$this->formName]['error'];

            return $this;
        }
    }

    /**
     * 保存文件至路径.
     *
     * @param string $savePath 目录
     * @param string $fileName 文件名，如果不给则用系统随机的文件名
     *
     * @return static
     */
    public function save($savePath, $fileName = null)
    {
        $fileName = null === $fileName ? $this->attributes['fileName'] : $fileName;
        !empty($this->attributes) && move_uploaded_file($_FILES[$this->formName]['tmp_name'], rtrim($savePath, '/') . '/' . $fileName);

        return $this;
    }

    public function randomFileName($fileName)
    {
        return date('YmdHis') . Strings::random(10) . '.' . $this->getExt($fileName);
    }

    public function getExt($fileName)
    {
        return pathinfo($fileName, PATHINFO_EXTENSION);
    }

    /**
     * 文件上传的错误码
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * 文件上传是否成功
     *
     * @return boolean
     */
    public function success()
    {
        return self::ERROR_SUCCESS === $this->errorCode;
    }

    /**
     * 文件上传的错误信息.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return self::$errorMap[$this->errorCode];
    }

    /**
     * 返回上传后文件的属性.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    // private

    private function getSizeLimit()
    {
        return min(Convert::size(Env::get('upload_max_filesize', 0)), Convert::size(Env::get('post_max_size', 0)));
    }
}
