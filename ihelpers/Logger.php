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
        if ($this->config['useFile']) {
            $logPath = BaseI::getAlias(trim($this->config['filePath'], '/') . '/');
            $logString = date('Y-m-d H:i:s') . " [{$errfile}]({$errno})第 {$errline} 行：{$errstr}" . PHP_EOL;
            FileIO::createDir($logPath);
            file_put_contents($logPath . date('Y-m-d') . '.log', $logString, FILE_APPEND);
        }
    }
}