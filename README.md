<h1 align="left"><a href="https://www.kuzuozhou.cn">Unionpay非官方开发包</a></h1>

银联云闪付小程序非官方开发包

## Requirement

1. PHP >= 5.6
2. mcrypt扩展
3. **[Composer](https://getcomposer.org/)**

## Installation

```shell
$ composer require cfn/unionpay
```

## Usage

基本使用（以服务端为例）:

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

更多请参考 [https://www.kuzuozhou.cn/](https://www.kuzuozhou.cn/)。

## Contact Us

邮箱：cfn@leapy.cn
微信：SH-CFN

## License

MIT
