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
| MiniProgram<br/>小程序 | BackendToken<br/>获取应用访问令牌backendToken | getToken<br/>`backendToken` 是应用的服务端 API 的访问令牌，控制对服务端 API 的访问。`backendToken` 的有效期通过接口返回，目前设置为 7200 秒，接入方获取相应基础访问令牌后，需放入缓存，定期更新。 |
| MiniProgram<br/>小程序 | BackendToken<br/>获取应用访问令牌backendToken | getRefreshedToken<br/>刷新并获取应用访问令牌backendToken |
| MiniProgram<br/>小程序 | AccessToken<br/>获取授权访问令牌accessToken | getToken<br/>获取用户授权访问令牌 `accessToken` , 经过用户授权完成后，可通过授权访问令牌调用对应权限可访问的服务端 API 。 |
| MiniProgram<br/>小程序 | AccessToken<br/>获取授权访问令牌accessToken | getRefreshedToken<br/>刷新并获取授权访问令牌accessToken |
| MiniProgram<br/>小程序 | FrontToken<br/>基础访问令牌 | getToken<br/>调用upsdk 的基础访问令牌 |
| MiniProgram<br/>小程序 | FrontToken<br/>基础访问令牌 | getRefreshedToken<br/>刷新并获取upsdk的基础访问令牌 |
| MiniProgram<br/>小程序 | Mobile<br/>获取手用户机号 | getMobile<br/>通过用户手机号授权 `scope.mobile` 完成后，通过该接口获取用户手机号。 |
| MiniProgram<br/>小程序 | Auth<br/>获取用户实名信息 | getAuth<br/>通过用户实名授权 `scope.auth` 完成后，通过该接口获取用户实名信息。 |
| MiniProgram<br/>小程序 | Card<br/>获取用户绑定的银行卡列表（仅限银行小程序使用） | getCardList<br/>通过用户银行卡授权 `scope.bank` 完成后，银行小程序可获取用户绑定的该行银行卡列表信息。 |
| MiniProgram<br/>小程序 | CardToken<br/>获取用户授权卡token | getCardToken<br/>通过用户授权卡 token `scope.token` ，获取用户授权指定卡 `token` 。 |
| MiniProgram<br/>小程序 | UserStatus<br/>查询用户状态 | getUserStatus<br/>通过该接口可判断用户当前状态 |
| MiniProgram<br/>小程序 | Crypto<br/>加解密 | encrypt<br/>3DES加密（加密信息） |
| MiniProgram<br/>小程序 | Crypto<br/>加解密 | decrypt<br/>3DES解密（解密信息例如解密手机号） |
| Payment<br/>手机支付控件 | Order<br/>消费接口 | pay<br/>获取银联受理订单号（TN号） |

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
    'appid' => '*********', // appid
    'secret' => '*********', // 密钥
    'symmetricKey' => '*********', // 对称密钥
    'merId' => '*********', // 商户编号
    'signCertPath' => '*********', // 签名证书路径pfx结尾
    'signCertPwd' => '*********', // 签名证书密码
    'encryptCertPath' => '*********', // 敏感信息加密证书路径 cer结尾
    'debug' => False // debug模式
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
// 第一个参数就是获取access_token时返回的openid，第二个参数代表是否解密
$mobile = $app->mobile->getMobile('YbknmZra+VRPee76j+IVFeQHQ0vQ3pAZHVaCw7ovJQk/jTof+GMd6DSDRQAf/gaf', false);

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

### 获取TN号支付：

```php
<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/15 22:34
 * Copyright: php
 */

use unionpay\Factory;

$options = [
    'appid' => '*********', // appid
    'secret' => '*********', // 密钥
    'symmetricKey' => '*********', // 对称密钥
    'merId' => '*********', // 商户编号
    'signCertPath' => '*********', // 签名证书路径pfx结尾
    'signCertPwd' => '*********', // 签名证书密码
    'encryptCertPath' => '*********', // 敏感信息加密证书路径 cer结尾
    'debug' => False // debug模式
];

$app = Factory::payment($options);

// txnAmt支付金额（分），orderId商户订单号
$data = $app->order->pay(['txnAmt' => 1, 'orderId' => date('YmdHis').rand(1000,9999)]);

// 返回的结果已验签，不必再验签
print_r($data);

// 输出
// Array
// (
//     [bizType] => 000201
//     [txnSubType] => 01
//     [orderId] => **************
//     [txnType] => 01
//     [encoding] => utf-8
//     [version] => 5.1.0
//     [accessType] => 0
//     [txnTime] => **************
//     [respMsg] => 成功[0000000]
//     [merId] => **************
//     [tn] => **************
//     [signMethod] => 01
//     [respCode] => 00
//     [signPubKeyCert] => -----BEGIN CERTIFICATE-----
// MIIEKzCCAxOgAwIBAgIFEpVGRCEwDQYJKoZIhvcNAQEFBQAwITELMAkGA1UEBhMC
// Q04xEjAQBgNVBAoTCUNGQ0EgT0NBMTAeFw0yMDA3MTYwOTM4MzRaFw0yNTA3MTYw
// OTM4MzRaMIGWMQswCQYDVQQGEwJjbjESMBAGA1UEChMJQ0ZDQSBPQ0ExMRYwFAYD
// VQQLEw1Mb2NhbCBSQSBPQ0ExMRQwEgYDVQQLEwtFbnRlcnByaXNlczFFMEMGA1UE
// Aww8MDQxQDgzMTAwMDAwMDAwODMwNDBA5Lit5Zu96ZO26IGU6IKh5Lu95pyJ6ZmQ
// 5YWs5Y+4QDAwMDE2NDk0MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA
// r50XGgVgM+8NnK3fDoMkqy0E+KcnnA6lQflB0Oet1zemVIzzn+76tPS0vV02OcpV
// u9dPt5iq83pMKBLY9isUuyRUWz8fn8Z7o3KvBoCRK4edtui/ihUt5vysJ920s8aG
// CbBRAdRmdIa44ha6W61KEJqrhw5iI2QkDK6OgVxs7imXgYiMc5lxLQL+9bRRGbKq
// zCAidolds633dQC58GZCtKIGvnwuDo8GGVTtjci7OU4c+54vtss2aDnE4QfLY4OY
// 1y+YXqy0D8Pax9T8ZnX7op8rCcO7FyH+0xgYA6gGnFlE3puiqxCFXCD7QI0np/bA
// XuZ6tIoBrqKGvsUobVO3swIDAQABo4HzMIHwMB8GA1UdIwQYMBaAFNHb6YiC5d0a
// j0yqAIy+fPKrG/bZMEgGA1UdIARBMD8wPQYIYIEchu8qAQEwMTAvBggrBgEFBQcC
// ARYjaHR0cDovL3d3dy5jZmNhLmNvbS5jbi91cy91cy0xNC5odG0wOAYDVR0fBDEw
// LzAtoCugKYYnaHR0cDovL2NybC5jZmNhLmNvbS5jbi9SU0EvY3JsMjQ5NjMuY3Js
// MAsGA1UdDwQEAwID6DAdBgNVHQ4EFgQUQP9Yqy8KJGuiHVVGrE1k+OryQyYwHQYD
// VR0lBBYwFAYIKwYBBQUHAwIGCCsGAQUFBwMEMA0GCSqGSIb3DQEBBQUAA4IBAQBk
// OfvkzBq1GSgBCED+ETg15xpIz/Zujb9PkgNY0UinywYIjkn6dfluMIk2cNiCOMfM
// Rg6LhtFi01Fnn3qwHe2vCEVBPJlazSsFE61tRCBTTWm8p/zfZKI9wGyir5aYBiPC
// TRPgXaQ4cYqSAh1n98a4ONBy2/StBl+TfKvCIoXARUSp12lOVY/aKg+8Jk4MIvEw
// 8WCL98tTVxXe1nWPlpFDS9y0ivMyfYlWkTb6+0gMrYA2nzrfFGS1KZNRBS7p3Bh5
// tdBPIgSd5gLZpAun8d0C3CcRZhcIof9hmxIc9ieQoWas52oVZDzsaGTo9rsTo9nU
// 3N3BThugW+P/koUnIFRG
// -----END CERTIFICATE-----
//     [signature] => fxLxEKV4GGLpvUnYsaCILUh6YyYI/jgwdeh94dGrT75nwGCOnspmB06cuzNj7G47mIR/TJZ0EEafJjaL2gkanVQMk4RfSMWGc+xcj8IYhdprbqZHyy7tbMCIMCDRlz1QKK2+UXXHs+dYDWHwqp3t4ZXpZ/GkmFNCRuExtzCcdotgzLGAc6PhGCKmL0nKC+ekGB48uLsg3lsmSTO08RUk9G32cOxqcFoVjhDJRjeqnccBo16GEjOT8TiyJOqFiG8Jk+E3ZZcYo1JM1FVRzR7TXuVcxEJmdePM3Akmtxa9MsuHMM0YP8YqPwN9Z9PH72fAplsAFPCAwhQrCeNjX6+f9g==
//)
```

> 更多示例请参考 [https://www.kuzuozhou.cn/](https://www.kuzuozhou.cn/)。

## 作者

邮箱：cfn@leapy.cn<br/>
微信：SH-CFN

## 支持

您的支持就是我们最大的动力，本项目接受任何形式的捐赠，您也可以star支持本项目。

## License

MIT
