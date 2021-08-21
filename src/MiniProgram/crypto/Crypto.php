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
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 20:30
     */
    public function decrypt($data)
    {
        if (!isset($this->config['symmetricKey']) || empty($this->config['symmetricKey']))
            throw new \Exception('对称密钥未配置！');
        return Encrypt::decrypt3DES($data, $this->config['symmetricKey']);
    }

    /**
     * 加密
     * @param $data
     * @return string
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 20:30
     */
    public function encrypt($data)
    {
        if (!isset($this->config['symmetricKey']) || empty($this->config['symmetricKey']))
            throw new \Exception('对称密钥未配置！');
        return Encrypt::encrypt3DES($data, $this->config['symmetricKey']);
    }

    /**
     * 签名
     * @param $data
     * @return string
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/21 0:13
     */
    public function sign($data)
    {
        if (!isset($this->config['privateKey']) || empty($this->config['privateKey']))
            throw new \Exception('加密rsa私钥未配置！');
        return Encrypt::sign($data,$this->config['privateKey']);
    }

    /**
     * 验签
     * @param $data
     * @return bool
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/21 0:13
     */
    public function verify($data)
    {
        if (!isset($this->config['publicKey']) || empty($this->config['publicKey']))
            throw new \Exception('银联公钥未配置！');
        return Encrypt::verify($data, $this->config['publicKey']);
    }
}
