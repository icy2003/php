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

- 使用 `composer` 安装时，`Yii/Yii2` 配置项里添加

    ```json
     '@icy2003' => '@vendor/icy2003/icy2003_php',
    ```

- 其他项目中，`icy2003_php` 这层的命名空间是 `icy2003`，具体请根据对应项目自行设置

## 重要更新

见底部

# icy2003's PHP library

## WHY ME?

- Repaired an unusual bug in the former `Curl` class.

- More contents such as: new functions, APIs, practice things.

- Convenient to use, Continual updates.

- Because of my cuteness. ～(ღゝ◡╹)ノ♡

## Address

-  [github](https://github.com/icy2003/icy2003_php)

-  [packagist](https://packagist.org/packages/icy2003/icy2003_php)


## Installation

**Composer**

Suggested use composer for the installation, because it's still in developing, and you need to bring `dev-master` after the composer command.

```cmd
composer require icy2003/icy2003_php dev-master
```

In that case, you can update me with the composer command `composer update` easily.

**Common installation**

`clone` it in your project.

## Usage

- The direct usages are put in the folder named `samples`, run `run.bat` to test them, it will start a built-in server of PHP, then visits them at `http://localhost:8000/[PHP demos]` on your brower.

- By using `composer`, you need to add the following configuration item in `Yii/Yii2`
    ```json
     '@icy2003' => '@vendor/icy2003/icy2003_php',
    ```

- `icy2003` will be the namespace of `icy2003_php` folder,so you can update your configuration accordding to it in your own project.

## Important updates

Only lists the new things, see details in git log.

See details at the bottom of this page(Chinese version).

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