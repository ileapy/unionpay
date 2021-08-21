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
| MiniProgram<br/>小程序 | User<br/>获取手用户机号 | mobile<br/>通过用户手机号授权 `scope.mobile` 完成后，通过该接口获取用户手机号。 |
| MiniProgram<br/>小程序 | User<br/>获取用户实名信息 | auth<br/>通过用户实名授权 `scope.auth` 完成后，通过该接口获取用户实名信息。 |
| MiniProgram<br/>小程序 | User<br/>获取用户绑定的银行卡列表（仅限银行小程序使用） | card<br/>通过用户银行卡授权 `scope.bank` 完成后，银行小程序可获取用户绑定的该行银行卡列表信息。 |
| MiniProgram<br/>小程序 | User<br/>获取用户授权卡token | cardToken<br/>通过用户授权卡 token `scope.token` ，获取用户授权指定卡 `token` 。 |
| MiniProgram<br/>小程序 | User<br/>查询用户状态 | userStatus<br/>通过该接口可判断用户当前状态 |
| MiniProgram<br/>小程序 | Contract<br/>申请签约 | apply<br/>无感支付签约接口，用户无感签约 scope:upapi_contract 授权且点击”同意授权”后，后台可通过此接口发起支付签约，签约完成后根据协议号走全渠道即可发起交易。 |
| MiniProgram<br/>小程序 | Contract<br/>申请解约 | relieve<br/>无感支付解约，使用接入方签约时传入的”签约协议号”，通过此接口可发起无感支付解约。|
| MiniProgram<br/>小程序 | Contract<br/>签约关系查询 | signStatus<br/>查询用户与接入方的签约关系状态，5分钟缓存机制，为保证查询结果准确性，建议间隔5分钟以上再进行状态查询。|
| MiniProgram<br/>小程序 | Contract<br/>未完成订单查询 | unFinishedOrder<br/>查询已签约用户在云闪付侧是否存在业务未完成的订单。|
| MiniProgram<br/>小程序 | Message<br/>小程序模板消息 | send<br/>通过小程序运营平台配置小程序消息模板，通过该接口给云闪付用户推送小程序消息, 例如服务通知、动账通知。 |
| MiniProgram<br/>小程序 | Secure<br/>获取验证支付密码流水号 | verifyPwd<br/>获取验证支付密码流水号，用于唤起验证支付密码控件。|
| MiniProgram<br/>小程序 | Redpack<br/>赠送专享红包 | given<br/>接入方给云闪付用户赠送专享红包。|
| MiniProgram<br/>小程序 | Redpack<br/>机构账户（红包）余额查询 | orgQuery<br/>查询银联红包接入方账户余额查询。|
| MiniProgram<br/>小程序 | Redpack<br/>专享红包余额查询 | excQuery<br/>根据专享红包活动 id 查询用户的该专享红包余额。|
| MiniProgram<br/>小程序 | Qual<br/>抽奖资格赠送 | given<br/>接入方给云闪付用户赠送抽奖资格。|
| MiniProgram<br/>小程序 | Qual<br/>抽奖资格查询 | query<br/>接入方查询云闪付用户的抽奖资格。|
| MiniProgram<br/>小程序 | Qual<br/>抽奖 | lotto<br/>接入方通过资格赠送云闪付抽奖（红包/票券）接口资格，给用户赠送（非定额/定额）红包或者商城券。|
| MiniProgram<br/>小程序 | Qual<br/>直接抽奖 | directLotto<br/>用于营销活动直接抽奖。|
| MiniProgram<br/>小程序 | Coupon<br/>赠送优惠券 | given<br/>赠送优惠券接口，根据营销活动给云闪付用户赠送优惠券。|
| MiniProgram<br/>小程序 | Coupon<br/>赠送优惠券结果查询 | query<br/>赠送优惠券结果查询。|
| MiniProgram<br/>小程序 | Coupon<br/>优惠券活动剩余名额查询 | quota<br/>返回营销系统配置的优惠券活动剩余名额。|
| MiniProgram<br/>小程序 | Face<br/>人脸识别 | image<br/>获取人脸识别照片|
| MiniProgram<br/>小程序 | Face<br/>人脸识别 | video<br/>获取人脸识别视频 |
| MiniProgram<br/>小程序 | Base<br/>小程序初始化配置 | config<br/>获取初始化配置参数（可开启debug模式） |
| MiniProgram<br/>小程序 | Crypto<br/>加解密 | encrypt<br/>3DES加密（加密信息） |
| MiniProgram<br/>小程序 | Crypto<br/>加解密 | decrypt<br/>3DES解密（解密信息例如解密手机号） |
| MiniProgram<br/>小程序 | Crypto<br/>加解密 | verify<br/>验签（银联公钥验签） |
| MiniProgram<br/>小程序 | Crypto<br/>加解密 | sign<br/>签名(rsa私钥加签) |
| MiniProgram<br/>小程序 | Notify<br/>小程序统一回调 | unit<br/>对消息通知进行统一处理 |
| Payment<br/>手机支付控件 | Order<br/>消费接口 | pay<br/>消费是指境内外持卡人在境内外商户网站进行购物等消费时用银行卡结算的交易，经批准的消费额将即时 地反映到该持卡人的账户余额上。(TN号获取) |
| Payment<br/>手机支付控件 | Order<br/>消费撤销接口 | cancel<br/>是指因人为原因而撤销已完成的消费，商户可以通过SDK向银联全渠道支付平台发起消费撤销交易，消费撤销必须是撤销CUPS当日当批的消费。发卡行批准的消费撤销金额将即时地反映到该持卡人的账户上。完成交易的过程不需要同持卡人交互，属于后台交易。 |
| Payment<br/>手机支付控件 | Order<br/>退货接口 | refund<br/>在消费交易发生一段时间之内，由于持卡人或者商户的原因需要退款时，商户可以通过退货接口将支付款退还给持卡人，银联将在收到退货请求并且验证成功之后，按照退货规则让发卡行按照原路退到持卡人支付卡上。 |
| Payment<br/>手机支付控件 | Order<br/>交易状态查询接口 | query<br/>该接口提供所有银联订单的查询，包括支付、退货、消费撤销交易。商户可以通过查询订单接口主动查询订单状态，完成下一步的业务逻辑。 |
| Payment<br/>手机支付控件 | Preorder<br/>预授权接口 | pay<br/>预授权交易用于受理方向持卡人的发卡方确认交易许可。受理方将预估的消费金额作为预授权金额，发送给持卡人的发卡方。|
| Payment<br/>手机支付控件 | Preorder<br/>预授权撤销 | cancel<br/>对已成功的POS预授权交易，在结算前使用预授权撤销交易，通知发卡方取消付款承诺。预授权撤销交易必须是对原始预授权交易或追加预授权交易最终承兑金额的全额撤销。|
| Payment<br/>手机支付控件 | Preorder<br/>预授权完成撤销 | refund<br/>预授权完成撤销交易必须是对原始预授权完成交易的全额撤销。预授权完成撤销后的预授权仍然有效。 |
| Payment<br/>手机支付控件 | Preorder<br/>预授权完成 | finish<br/>对已批准的预授权交易，用预授权完成做支付结算。 |
| Payment<br/>手机支付控件 | Notify<br/>回调统一处理 | unit<br/>对消费接口，消费撤销接口，退货接口，预授权接口，预授权撤销接口，预授权完成接口，预授权完成撤销接口的回调统一处理 |
| Payment<br/>手机支付控件 | Signature<br/>加签验签 | validate<br/>对返回数据进行验签 |
| Payment<br/>手机支付控件 | Signature<br/>加签验签 | sign<br/>对发生数据进行加签 |
| Payment<br/>手机支付控件 | File<br/>文件传输类交易接口 | download<br/>商户可以通过此接口获取对账文件。|
| Payment<br/>手机支付控件 | File<br/>文件保存方法 | save<br/>对获取的对账文件进行保存 |
| Payment<br/>手机支付控件 | File<br/>银联加密公钥更新查询接口（此接口暂时不可用，银联没详细文档） | updatePublicKey<br/>商户定期（1天1次）向银联全渠道系统发起获取加密公钥信息交易。在加密公钥证书更新期间，全渠道系统支持新老证书的共同使用，新老证书并行期为1个月。全渠道系统向商户返回最新的加密公钥证书，由商户服务器替换本地证书。 |

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
// 参数解析
//* @param string $code 用户授权或静默授权获取的code和openid必传其一 不是必填项
//* @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口 不是必填项
//* @param bool $decrypt 是否解密返回 不是必填项

// 第一种方式 通过code直接获取
$mobile = $app->user->mobile("nSuRv/iJQm+6wYE6sqRx8w==");

// 第二种方式 通过openid获取，调用这种方式之前请确保已先调用accessToken方法
$mobile = $app->user->mobile(null,'YbknmZra+VRPee76j+IVFeQHQ0vQ3pAZHVaCw7ovJQk/jTof+GMd6DSDRQAf/gaf', false);


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

### 小程序消息通知：

```
// 默认自动验签，第二个参数设置为false就不自动验签，可自行验签, 注意验签失败会抛异常
$response = $app->notify->unit(function ($message) use ($app){
    // 自行验签
    $res = $app->crypto->verify($message);
    if ($res) {
        // 验签成功
        echo "验签成功";
    } else {
        // 验签失败
        echo "验签失败";
    }
}, false);

$response->send();
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

// 对返回的结果进行验签
if ($app->signature->validate($data))
{
    // todo 验签成功
}

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

### 支付回调统一处理：

```
// 默认自动验签，第二个参数设置为false就不自动验签，可自行验签, 注意验签失败会抛异常
$response = $app->notify->unit(function ($message) use ($app){
    // 自行验签
    $res = $app->signature->validate($message);
    if ($res) {
        // 验签成功
        echo "验签成功";
    } else {
        // 验签失败
        echo "验签失败";
    }
}, false);

$response->send();
```

> 更多示例请参考 [https://www.kuzuozhou.cn/](https://www.kuzuozhou.cn/)。

## 作者

邮箱：cfn@leapy.cn<br/>
微信：SH-CFN

## 支持

您的支持就是我们最大的动力，本项目接受任何形式的捐赠，您也可以star支持本项目。

## License

MIT
