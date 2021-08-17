<h1 align="left"><a href="https://www.kuzuozhou.cn">unionpay 云闪付小程序开发包</a></h1>

银联云闪付小程序非官方开发包，这可能是第一个支持composer导入的云闪付小程序开发包。

码云地址 [https://gitee.com/leapy/unionpay](https://gitee.com/leapy/unionpay)

[![cfn/unionpay 云闪付小程序开发包](https://gitee.com/leapy/unionpay/widgets/widget_card.svg?colors=4183c4,ffffff,ffffff,e3e9ed,666666,9b9b9b)](https://gitee.com/leapy/unionpay)

## 依赖

1. PHP >= 5.6
2. mcrypt扩展
3. **[Composer](https://getcomposer.org/)**

## 安装

```shell
$ composer require cfn/unionpay
```

## 进度

| 模块名称       | 使用场景         | 接口方法                               |
| ------------------------- | ------------------ | ------------------------------------------ |
|MiniProgram<br/>小程序 | BackendToken<br/>应用访问令牌 | getToken<br/>获取应用访问令牌backendToken |
| MiniProgram<br/>小程序 | BackendToken<br/>应用访问令牌 | getRefreshedToken<br/>刷新并获取应用访问令牌backendToken |
| MiniProgram<br/>小程序 | AccessToken<br/>授权访问令牌 | getToken<br/>获取授权访问令牌accessToken |
|MiniProgram<br/>小程序 | AccessToken<br/>授权访问令牌 | getRefreshedToken<br/>刷新并获取授权访问令牌accessToken |
| MiniProgram<br/>小程序 | FrontToken<br/>基础访问令牌 | getToken<br/>调用upsdk 的基础访问令牌 |
| MiniProgram<br/>小程序 | FrontToken<br/>基础访问令牌 | getRefreshedToken<br/>刷新并获取upsdk的基础访问令牌 |
|MiniProgram<br/>小程序 | Mobile<br/>获取手机号 | getMobile<br/>获取手机号加密字符串 |
| MiniProgram<br/>小程序 | Crypto<br/>加解密 | encrypt<br/>3DES加密（加密信息） |
| MiniProgram<br/>小程序 | Crypto<br/>加解密 | decrypt<br/>3DES解密（解密信息解密例如手机号） |


还有更多在积极适配中......

## 示例

### 获取BackendToken:

```php
<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/15 22:34
 * Copyright: php
 */

use unionpay\Factory;

$options = [
    'appid' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxx',
    'secret' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
    'symmetricKey' => 'xxxxxxxxxxxxxxxxxxxxxxxxx',
    'debug' => true,
    'merId' => 'xxxxxxxxxxxxxxxx', // 支付时使用
    'pfx' => '', // 支付时使用
    'pwd' => 'xxxxxxxxxxxxxxxxx', // 支付时使用
    'cer' => '' // 支付时使用
];

$app = Factory::miniProgram($options);


$backendToken = $app->backend_token->getToken(false);

print_r($backendToken);

// 输出：
//Array
//(
//    [backendToken] => GLnIGxENS6eDke7hvrZVjQ==
//    [expiresIn] => 7200
//)

```

### 获取AccessToken：

```php
$accessToken = $app->access_token->getToken('DD8jVkc1TwaCSohdazL8+w==',false);

print_r($accessToken);

// 输出
//Array
//(
//    [expiresIn] => 3600
//    [unionId] => 77e5ad3128eade6dd1222fbf722a7a1a7becfe9d715ace8841d429d29a87a700
//    [openId] => YbknmZra+VRPee76j+IVFeQHQ0vQ3pAZHVaCw7ovJQk/jTof+GMd6DSDRQAf/gaf
//    [scope] => upapi_mobile
//    [accessToken] => J39i0JZlD8uLxF3uEM6DzynLYJatTaSOmYah1ybVTnYy3dq7lTZzaLI1c/fJbQ+k+0eRdGj9fcx4BFtBbqu5VN/wAbP2aodWmuHQt5vKkMg=
//    [refreshToken] => DACgw7edTz29xCx9lcjwhw==
//)

```

### 获取FrontToken：

```php
$frontToken = $app->front_token->getToken();

print_r($frontToken);

// 输出
//Array
//(
//    [expiresIn] => 7200
//    [frontToken] => YO1p4AFJRCy0TLZMu4MNHw==
//)
```

### 获取手机号加密字符串：

```php
$mobile = $app->mobile->getMobile('YbknmZra+VRPee76j+IVFeQHQ0vQ3pAZHVaCw7ovJQk/jTof+GMd6DSDRQAf/gaf');

print_r($mobile);

// 输出
//Array
//(
//    [mobile] => Bth5XXdhUQIQLYXOcAreTQ==
//)
```

### 手机号解密：

```php
$mobile = $app->crypto->encrypt('Bth5XXdhUQIQLYXOcAreTQ==');

var_dump($mobile);

// 输出
//string(11) "1**********"
```

> 更多示例请参考 [https://www.kuzuozhou.cn/](https://www.kuzuozhou.cn/)。

## 作者

邮箱：cfn@leapy.cn<br/>
微信：SH-CFN

## 支持

您的支持就是我们最大的动力，本项目接受任何形式的捐赠，您也可以star支持本项目。

## License

MIT
