<?php

namespace icy2003\ihelpers;


$defaultConfig = __DIR__ . '/../config.php';
defined("I_DEFAULT_CONFIG_FILE") || define("I_DEFAULT_CONFIG_FILE", $defaultConfig);
defined("I_CONFIG_FILE") || define("I_CONFIG_FILE", $defaultConfig);

class Logger
{
    private static $instance;
    private $config;
    private $file;
    private $db;

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
            $config = Arrays::arrayMergeRecursive(require_once I_DEFAULT_CONFIG_FILE, require_once I_CONFIG_FILE, $config);
            self::$instance->config = $config['Logger'];
            self::$instance->file = self::$instance->config['file'];
        }

        return self::$instance;
    }
    public function run()
    {
        set_error_handler([$this, 'errorHandler']);
    }
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($this->file) {
            $logPath = trim($this->config['file_path'], '/') . '/';
            $logString = date('Y-m-d H:i:s') . " [{$errfile}]({$errno})第 {$errline} 行：{$errstr}" . PHP_EOL;
            FileIO::createDir($logPath);
            file_put_contents($logPath . date('Y-m-d') . '.log', $logString, FILE_APPEND);
        }
    }

    public static function defaultConfig()
    {
        return require_once I_DEFAULT_CONFIG_FILE;
    }
}