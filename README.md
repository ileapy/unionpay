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
// 第一个参数就是access_token获取到的openid，第二个参数代表是否解密
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

> 更多示例请参考 [https://www.kuzuozhou.cn/](https://www.kuzuozhou.cn/)。

## 作者

邮箱：cfn@leapy.cn<br/>
微信：SH-CFN

## 支持

您的支持就是我们最大的动力，本项目接受任何形式的捐赠，您也可以star支持本项目。

## License

MIT
