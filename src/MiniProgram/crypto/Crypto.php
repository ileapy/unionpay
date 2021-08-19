<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/16 20:19
 * Copyright: php
 */

namespace unionpay\MiniProgram\crypto;

use unionpay\Kernel\Client\MiniProgramClient;
use unionpay\Kernel\Support\Encrypt;
use unionpay\Kernel\Support\Str;

/**
 * Class Crypto
 *
 * @package unionpay\MiniProgram\des
 */
class Crypto extends MiniProgramClient
{
    /**
     * 解密
     * @param $data
     * @return string
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 20:30
     */
    public function decrypt($data)
    {
        return Encrypt::decrypt3DES($data, $this->config['symmetricKey']);
    }

    /**
     * 加密
     * @param $data
     * @return string
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 20:30
     */
    public function encrypt($data)
    {
        return Encrypt::encrypt3DES($data, $this->config['symmetricKey']);
    }

    /**
     * 银联公钥验签
     * @param array $data 原始数据
     * @return bool
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/19 19:13
     */
    public function verify($data)
    {
        $signature = $data['signature'];
        unset($data['signature']);
        $res = openssl_get_publickey($this->config['publicKey']);
        $result = (bool)openssl_verify(Str::sortByASCII($data), base64_decode($signature), $res,'SHA256');
        openssl_free_key($res);
        return $result;
    }
}
