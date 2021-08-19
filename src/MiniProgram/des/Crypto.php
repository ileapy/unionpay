<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/16 20:19
 * Copyright: php
 */

namespace unionpay\MiniProgram\des;

use unionpay\Kernel\Client\MiniProgramClient;
use unionpay\Kernel\Support\TripleEncrypt;

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
        return TripleEncrypt::decrypt3DES($data, $this->config['symmetricKey']);
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
        return TripleEncrypt::encrypt3DES($data, $this->config['symmetricKey']);
    }
}
