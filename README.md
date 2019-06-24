# icy2003 的 PHP 库

小孩子才做选择，大人当然全都要！下载 **icy2003/php**，你就相当于拥有了瑞士军刀（PHP）中的瑞士军刀（**icy2003/php**）

**icy2003/php** 拥抱 composer，如果你还不会使用它，建议你先看看这个：[composer 中文文档](https://docs.phpcomposer.com/)

## 文档

**icy2003/php** 是基于 [phpdocumentor](https://www.phpdoc.org/) 标准格式写的注释，因此你可以轻易使用 phpdocumentor 生成一份完整详细的文档。

phpdocumentor 生成的 **docs/.htaccess** 会导致个小 bug，请用我写的替换掉，两行命令搞定：

```shell
composer require phpdocumentor/phpdocumentor 2.*
# windows
./vendor/bin/phpdoc.bat -d ./src -t ./docs
# linux
./vendor/bin/phpdoc -d ./src -t ./docs
```

## 功能说明

目前分为四个部分：助手函数（ihelpers）、其他库扩展（iexts）、组件（icomponents）和第三方接口（iapis）。

以及一个最常用的通用静态类：I.php，下面是它的一些重要方法：

- get：猜猜 `I::get($array, 'a.b.c')` 想干嘛，没错，get 方法可以用来获取**任意维数组的值**，你也可以在找不到数据的时候设置默认值，和三元操作符甚至一堆 if 语句说永别吧。顺便一提，它还支持**对象操作**以及它们的**混合**！更多复杂的应用请查看该函数的注释

- def：`defined($constant) || define($constant, $value);` 而已，我实在不想敲那么多字

- displayErrors：显示 PHP 错误，同样是一个使用频率很高的操作

- getAlias：了解 Yii2 的人看这个函数的实现应该有点眼熟，这里对 Yii2::getAlias 进行了改进，假设你有一个蛋疼的需求：想把 a/b 的别名设置为 @aa，把 a/b/c 的别名设置为 **@cc**，那 I::getAlias 可以帮你完成，而 Yii2::getAlias 会把 a/b/c 当成 **@aa/c**，如果你使用 Yii2，那你完全可以用 I::getAlias 代替 Yii2::getAlias

- setAlias：你需要使用这个来设置别名

### ihelpers

涉及的方法太多，简单说明一些：

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
- Strings：随机数、密码以及校验、驼峰下划线转换等字符串操作
- Timer：计时器
- Upload：文件的表单上传
- View：视图渲染
- Xml：Xml 操作类

**icy2003/php** 的方法命名追求一种看名知意的约定，以 Xml 类为例子，一般我们拿到 Xml 文本都是要转成数组的，那么，这种格式转换的类基本会有这些方法：

- `Xml::toArray`：“Xml 文本转成数组”
- `Xml::fromArray`：“Xml 文本来源于数组”，换个说法就是“数组转换成了 Xml 文本”
- `Xml::isXml`：“是否是 Xml 文本”
- `Xml::get`：该方法会先执行 `toArray`，然后调用 `I::get`

另外，像 Json 类里，会根据习惯优先使用以下命名：

- `Json::encode`：数组转 JSON 字符串
- `Json::decode`：JSON 字符串转数组

### iexts

目前是 yii2 和 phpoffice 的一些问题的修复。
顺便一提 phpoffice，作者偷偷告诉我，他正在计划重构这货（phpoffice），的确，现在的 phpoffice 有很多不尽如人意的问题，期待一下吧，你也许可以从这里找到一些好用的方法（至少是 phpoffice 没有提供或者有问题的）

### icomponents

- file：对文件和目录的操作。例如创建、删除、复制、移动目录，写入、读取、删除、复制、移动、上传、下载文件等，并且它们尽可能会是递归操作，所以你不再需要担心“该目录下存在文件”这种报错。支持本地文件、ftp、sftp！更多环境还有三十秒到达战场……
- cache：缓存组件。目前只有文件缓存，此组件还在开发中。
- excel：Excel 函数的实现。Excel 里有很多有意思的数学计算，为什么不移植过来呢？

### iapis

正在开发中，暂时计划会全部实现微信和支付宝的支付相关接口。

已经完成：

- 微信：统一下单、退款等

## 地址

-  [github](https://github.com/icy2003/php)

-  [packagist](https://packagist.org/packages/icy2003/php)

## 安装

composer 安装，暂时是开发版，请使用以下命令：

```cmd
composer require icy2003/php dev-master
```

`composer update` 的时候请选 `y`