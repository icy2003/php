# icy2003 的 PHP 库

## 为什么是我？

- 修复原 `Curl` 类里某个不常见的 bug

- 更多其他内容：新的函数库、API 接口、实践性的东西

- 使用方便，持续更新

- 因为我萌啊，～(ღゝ◡╹)ノ♡

## 地址

-  [github](https://github.com/icy2003/icy2003_php)

-  [packagist](https://packagist.org/packages/icy2003/icy2003_php)


## 安装

**composer**

建议使用 composer 安装，暂时是开发版，所以后面需要带上 `dev-master`

```cmd
composer require icy2003/icy2003_php dev-master
```

这样就可以方便地用 `composer` 命令 `composer update` 更新我啦啦~

**常规**

`clone` 至你的项目中

## 使用

- 直接使用的例子放在 `samples\` 里，需要测试则执行 `run.bat`，将会开启 PHP 内置服务器，浏览器访问 `http://localhost:8000/[PHP 示例]`

- `Yii/Yii2` 建议使用这种方式，在配置项里添加

    ```json
     '@icy2003' => '@vendor/icy2003/icy2003_php',
    ```

- 其他 `composer` 项目，在你的 `composer.json` 里添加

    ```json
    "autoload": {
        "psr-4": {"icy2003\\": "vendor/icy2003/icy2003_php/"}
    }
    ```

- 其他非 `composer` 项目，可以使用我写的自动加载

    ```php
    require 'vendor/icy2003/icy2003_php/I.php';
    ```

## 重要更新

只标明新的东西，具体更新参见 git 日志

### 2018 03 19

- `isdk\APICloud\`: `APICloud` API 可用

- `isdk\RongCloud\`: 开始更新 `RongCloud` 接口，官方的有点看不下去了

- `composer.json`: 提供 composer 安装方式

### 2018 03 22

- `ipractices\ipatterns\`: 设计模式使用示例

- `ihelpers\`: `Base64`、`Charset`、`Timer` 类

### 2018 03 26

- `run.bat` 和 `samples\`: PHP 本地服务器以及示例文件夹

### 2018 08 08

- 只更新了 `README.md`，关于使用的其他一些方式。最近没搞 PHP，不过 `icy2003_php` 很快就会有新内容了~