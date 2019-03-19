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

## 目录说明

- `\src` 根目录

- `\src\iexts` 对其他 PHP 库的扩展：

    1. 命名空间为 `icy2003\php\[原库的类的命名空间]`

    2. 同名类表示继承于原类

    3. `i` 前缀的类里对应原类的同名方法，功能有所不同（因为原类难以继承，因此直接抽离出来）

- `\src\ihelpers` 一些帮助函数

其他文件和文件夹正在测试中，请不要过度依赖

## 其他

- 私有成员和方法以双下划线（__）开头，保护成员和方法用单下划线（_）开头，对于构造函数等原生 PHP 就带下划线的，视情况而定