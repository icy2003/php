# icy2003 的 PHP 库

## 地址

-  [github](https://github.com/icy2003/php)

-  [packagist](https://packagist.org/packages/icy2003/php)


## 安装

**composer**

建议使用 composer 安装，暂时是开发版，所以后面需要带上 `dev-master`

```cmd
composer require icy2003/php dev-master
```

这样就可以方便地用 `composer` 命令 `composer update` 更新我啦啦~

**常规**

`clone` 至你的项目中

## 使用

- 直接使用的例子放在 `samples\` 里，需要测试则执行 `run.bat`，将会开启 PHP 内置服务器，浏览器访问 `http://localhost:8000/[PHP 示例]`

- `Yii/Yii2` 建议使用这种方式，在配置项里添加

    ```json
     '@icy2003' => '@vendor/icy2003/php',
    ```

- 其他 `composer` 项目，在你的 `composer.json` 里添加

    ```json
    "autoload": {
        "psr-4": {"icy2003\\": "vendor/icy2003/php/"}
    }
    ```

- 其他非 `composer` 项目，可以使用我写的自动加载

    ```php
    require 'vendor/icy2003/php/I.php';
    ```