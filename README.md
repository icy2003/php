# icy2003 的 php 库

## 使用

### composer 安装

- 建议使用 composer 安装，暂时是开发版，所以后面需要带上 `dev-master`

    ```cmd
    composer require icy2003/icy2003_php dev-master
    ```

### 常规安装

- `clone` 至你的项目中

- 普通 php 文件，参考 `index_demo.php`

- 使用框架（如 `TP`，`Yii2`）时肯定使用框架自身的自动加载，不用像上面那样 `include` 一下

## 说明

- `Curl` 类不再依赖 `Yii2`，且不会有某种（斜眼笑）bug 的产生

## 更新

### 2018 03 19

- `APICloud` 目前可用，但是不准备更新

- 开始更新 `RongCloud` 接口，官方的有点看不下去了

- 提供 composer 安装方式

