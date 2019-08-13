# icy2003 的 PHP 库

**icy2003/php** 是一个工具集，它功能丰富、执行高效、注释完整、使用简单

[![Build Status](https://travis-ci.com/icy2003/php.svg?branch=master)](https://travis-ci.com/icy2003/php)
[![Total Downloads](https://poser.pugx.org/icy2003/php/downloads)](https://packagist.org/packages/icy2003/php)
[![License](https://poser.pugx.org/icy2003/php/license)](https://packagist.org/packages/icy2003/php)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/icy2003/php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/icy2003/php/)
[![Code Coverage](https://scrutinizer-ci.com/g/icy2003/php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/icy2003/php/?branch=master)

## 功能

包括但不限于以下列表：

1. 数组字符串的各种操作
2. 本地、ftp、sftp 等的文件操作，例如：批量删除、添加、复制、剪切文件（夹）
3. 编码转换、时间转换、请求处理、缓存、文本处理、数学计算、图片处理、常用正则等
4. 针对 yii2、phpoffice 等的扩展
5. 微信支付宝的支付、退款等
6. 百度 AI 接口

## 文档

**icy2003/php** 是基于 [phpdocumentor](https://www.phpdoc.org/) 标准格式写的注释，因此你可以轻易使用 phpdocumentor 生成一份完整详细的文档

```shell
composer require phpdocumentor/phpdocumentor 2.*
# windows
./vendor/bin/phpdoc.bat -d ./src -t ./docs
# linux
./vendor/bin/phpdoc -d ./src -t ./docs
```

## 地址

-  [github](https://github.com/icy2003/php)
-  [packagist](https://packagist.org/packages/icy2003/php)

## 安装

composer 安装，目前为开发版

```cmd
composer require icy2003/php dev-master
```

## 补充说明和未来计划

1. 目前不打算实现 db，推荐使用 yii2
2. 目前不打算实现二维码，请自行找库或者调用别家 API
3. 计划编写完整的测试用例
