# icy2003 的 PHP 库

## 地址

-  [github](https://github.com/icy2003/php)

-  [packagist](https://packagist.org/packages/icy2003/php)


## 安装

composer 安装，暂时是开发版，请使用以下命令：

```cmd
composer require icy2003/php dev-master
```

`composer update` 的时候请选 `y`

## 功能说明

目前包括的类

- \I：一些常用操作
- \ihelpers\Arrays：数组操作
- \ihelpers\Base64：Base64 相关
- \ihelpers\Charset：编码转换
- \ihelpers\Color：处理颜色
- \ihelpers\Console：控制台相关
- \ihelpers\Convert：进制转换、单位转换等
- \ihelpers\DateTime：日期相关
- \ihelpers\Db：一个简单的数据库操作类
- \ihelpers\File：文件操作，如文件目录的创建删除等，文件读取等
- \ihelpers\Header：header 函数的实际应用
- \ihelpers\Html：Html 相关
- \ihelpers\Http：Http 请求
- \ihelpers\Json：Json 相关
- \ihelpers\Language：语言文件相关（可用，待定）
- \ihelpers\Logger：日志操作（不可用，暂不维护）
- \ihelpers\Magic：一个奇怪的类
- \ihelpers\Markdown：Markdown 语法快捷函数
- \ihelpers\OpenSSL：加密相关（不可用，暂不维护）
- \ihelpers\Reflecter：反射类（可用，暂不维护）
- \ihelpers\Regular：正则相关
- \ihelpers\Request：请求相关，例如获取 GET 请求参数（$_GET）等
- \ihelpers\Strings：字符串操作
- \ihelpers\Timer：计时器
- \ihelpers\Upload：文件上传下载
- \ihelpers\Url：Url 编码等
- \ihelpers\Validator：验证器（可用，暂不维护）
- \ihelpers\Xml：Xml 相关

以及

iexts：
1. 对 Yii2 的扩展
2. 对 PhpOffice 的扩展


## 文档生成

用 [phpdocumentor](https://www.phpdoc.org/) 生成文档，记得 `docs/.htaccess` 要替换掉
