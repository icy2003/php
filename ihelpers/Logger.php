<?php

namespace icy2003\ihelpers;

use icy2003\BaseI;

class Logger
{
    private static $instance;
    private $config;

    private function __construct()
    {
    }

    private function __clone()
    {
    }
    /**
     * 创建日志对象
     *
     * @return static
     */
    public static function create($config = [])
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
            self::$instance->config = BaseI::config('Logger');
        }

        return self::$instance;
    }
    public function run()
    {
        $this->config['isLog'] && set_error_handler([$this, 'errorHandler']);
    }
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $config = $this->config;
        $typeArray = explode(',', $config['type']);
        $map = [
            '{date}' => date($config['dateFormat']),
            '{errfile}' => $errfile,
            '{errno}' => $errno,
            '{errline}' => $errline,
            '{errstr}' => $errstr,
        ];
        $string = str_replace(array_keys($map), array_values($map), $config['errorTemplete']);
        static::handler($config, $string);
    }
    /**
     * 让 echo 可以当函数使用
     *
     * @param string $string
     * @return void
     */
    public static function echo($string)
    {
        echo $string;
    }

    /**
     * 返回 var_dump 的值的字符串
     *
     * @param mixed $data
     * @return string
     */
    public static function varDump($data)
    {
        ob_start();
        var_dump($data);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public static function info($message)
    {
        $config = BaseI::config('Logger');
        if (false === $config['isLog']) {
            return;
        }
        $array = debug_backtrace();
        $map = [
            '{date}' => date($config['dateFormat']),
            '{file}' => $array[0]['file'],
            '{line}' => $array[0]['line'],
            '{message}' => $message,
        ];
        $string = str_replace(array_keys($map), array_values($map), $config['infoTemplete']);
        static::handler($config, $string);
    }

    private static function handler($config, $string)
    {
        $typeArray = explode(',', $config['type']);
        if (in_array('file', $typeArray)) {
            $logPath = BaseI::getAlias(trim($config['file']['filePath'], '/') . '/');
            FileIO::createDir($logPath);
            $fileName = $config['file']['fileName'];
            if (is_callable($fileName)) {
                $fileName = $fileName();
            }
            file_put_contents($logPath . $fileName, $string . PHP_EOL, $config['file']['flag']);
        }
        if (in_array('print', $typeArray)) {
            $function = $config['print']['function'];
            is_callable($function) && call_user_func($function, $string . PHP_EOL);
        }
    }
}