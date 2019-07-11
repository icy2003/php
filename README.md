# icy2003 的 PHP 库

**icy2003/php** 是一个工具库，它诞生的核心思想是：**功能丰富、简洁明了、执行高效、猜你所想**

[![Build Status](https://travis-ci.com/icy2003/php.svg?branch=master)](https://travis-ci.com/icy2003/php)
[![Total Downloads](https://poser.pugx.org/icy2003/php/downloads)](https://packagist.org/packages/icy2003/php)
[![License](https://poser.pugx.org/icy2003/php/license)](https://packagist.org/packages/icy2003/php)

**功能丰富**：涉及功能例如有：数组操作、字符串操作、文件操作、图片处理、微信支付宝支付等，如果说 PHP 作为工具是一把瑞士军刀，那 icy2003/php 会是 PHP 的瑞士军刀，从最开始我只是想获得随机字符串（`Strings::random`）开始，到此文档更新为止，icy2003/php 包含的方法已有 **700+** 个！尽管这只是 PHP 里的沧海一粟，但是足以应付 PHP 使用过程中大部分遇到的问题

**简洁明了**：方法的命名简洁明了，尽可能在有限的字符长度里完整表达一个方法的含义，但又不故意缩减字符。例如：`Xml::toArray` 表示 XML 转成数组，我没有把它命名为 Xml:convertToArray 或是 Xml::toArr，前者太啰嗦，后者过度省略

**执行高效**：最直接的就是我不使用双引号和使用 `===`，所有的封装最开始都是一碗面条，直到抽象为最基础的小石头，在这个城堡的构建过程中，每个小石头都有它最精简的表达方式，必要时我还会反复测试效率甚至寻找其他解决方法

**猜你所想**：众所周知，PHP 有<i style = "color:white">八</i>~~三~~大数据类型：数组、字符串、对象……是啊，尽管 PHP 建议你注意数据的类型（比如 PHP7 的类型约束），但相比那些强类型语言，PHP 太包容了！PHP 会猜测你使用的数据类型以及使用场景，自动转换
icy2003/php 更加包容！例如： `Arrays::count`，这个函数用于获取数组元素个数，你可能会想，这有什么好扩展的？不就是 `count` 么？不，如果有个蠢蛋传了个字符串进去呢？是不是应该像强类型语言那样，傻傻地跟着报错？又或者说，你只是想知道，偶数的元素有几个，这时候 Arrays::count 第二参数接收一个回调，用于筛选你要的那种元素，而如果你只是想找到等于某个字符串的元素，那么第二参数可以改为接收字符串，省去了回调里写的那个比较，比较自然涉及到 `===` 还是 `==`，第三参数正是这个作用，到此 count 函数才算扩展完成。这只是一个例子，icy2003/php 里有很多类似的例子，只是为了让你更少地关注这些细节，把更多精力集中到实际业务里

## 文档

**icy2003/php** 是基于 [phpdocumentor](https://www.phpdoc.org/) 标准格式写的注释，因此你可以轻易使用 phpdocumentor 生成一份完整详细的文档

```shell
composer require phpdocumentor/phpdocumentor 2.*
# windows
./vendor/bin/phpdoc.bat -d ./src -t ./docs
# linux
./vendor/bin/phpdoc -d ./src -t ./docs
```

**icy2003/php** 对编辑器的提示支持也很好，在使用过程中就可以看到对应函数的说明



## 功能说明

目前分为四个部分：助手函数（ihelpers）、其他库扩展（iexts）、组件（icomponents）和第三方接口（iapis）
以及一个最常用的通用静态类：I.php，下面是它的部分方法简单介绍：

- get：这个函数就是它名字本身的含义：“获取”，获取什么？很多，比如，无视数组层级获取值，获取字符串某个位置的字符等，详细使用可看注释，它几乎成为 icy2003/php 的核心函数了
- displayErrors：显示 PHP 错误，同样是一个使用频率很高的操作
- getAlias：熟悉 Yii2 的人应该很眼熟，它对 Yii2 的别名进行了改进（等你发现），当然你完全可以用 I::getAlias 代替 Yii2::getAlias

### ihelpers

以下只是部分展示，不代表所有类：

- Arrays：各式各样的数组操作！不怕没有你要的操作，就怕你想不到有这种操作！
- Charset：编码转换，别再为 UTF-8 烦恼了
- Color：十六进制、RGB、CMYK 的颜色值转换，也支持例如“red”的英文单词颜色转换
- DateTime：老板说：“我要一个下拉选择框，可以显示最近三天、上个星期、今年等的数据”，请用这个类甩他一脸！
- File：请使用 icomponents 的 file 系列，那里有你想要的
- Http：后端发送 GET、POST 请求，支持异步操作
- Image：缩放、裁剪、水印、翻转、旋转图片，生成验证码、后端输出图片等
- Json：简单的 Json 操作封装
- Markdown：快速组装 Markdown 文本
- Regular：常见正则表达式，以后请不要蠢蠢地写这些写烂的正则了好么？何况不一定准
- Request：移植于 Yii2 的用于接收前端数据的类
- Strings：随机数、密码以及校验、驼峰下划线转换等字符串操作，元老级别的一个类，同样是丰富的操作！
- Timer：计时器
- Upload：文件的表单上传
- View：视图渲染
- Xml：Xml 操作类

### iexts

目前是 Yii2 和 phpoffice 的一些问题的修复

顺便一提 phpoffice，作者不仅采用了我某个小功能的代码，作者还告诉我，他正在计划重构这货（phpoffice）。的确，现在的 phpoffice 有很多不尽如人意的问题，期待一下吧，你也许可以从这里找到一些好用的方法（至少是 phpoffice 没有提供或者有问题的）

### icomponents

- file：对文件和目录的操作。例如创建、删除、复制、移动目录，写入、读取、删除、复制、移动、上传、下载文件等，并且它们尽可能会是递归操作，所以你不再需要担心“该目录下存在文件”这种报错。支持本地文件、ftp、sftp！更多环境还有三十秒到达战场……
- cache：缓存组件。目前只有文件缓存，此组件还在开发中
- excel：Excel 函数的实现。Excel 里有很多有意思的数学计算，为什么不移植过来呢？

### iapis

暂时计划会全部实现微信和支付宝的支付相关接口

已经完成：

- 微信：统一下单、退款等
- 支付宝：APP 支付、退款等

## 地址

-  [github](https://github.com/icy2003/php)
-  [packagist](https://packagist.org/packages/icy2003/php)

## 安装

composer 安装，暂时是开发版，请使用以下命令：

```cmd
composer require icy2003/php dev-master
```

`composer update` 的时候无脑选右（Y）