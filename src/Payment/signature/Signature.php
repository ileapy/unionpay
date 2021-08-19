<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/19 10:49
 * Copyright: php
 */

namespace unionpay\Payment\signature;

use unionpay\Kernel\Client\PaymentClient;
use unionpay\Kernel\Support\AcpService;

/**
 * Class Verify
 *
 * @package unionpay\Payment\verify
 */
class Signature extends PaymentClient
{
    /**
     * @param array $params 验签参数
     * @return bool
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/19 10:51
     */
    public function validate($params)
    {
        return (bool)AcpService::validate($params,'', $this->config['cert']['middleCertPath'], $this->config['cert']['rootCertPath'], $this->config['debug']);
    }

    /**
     * @param array $params 签名参数
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/19 10:56
     * @throws \Exception
     */
    public function sign(&$params)
    {
        AcpService::sign($params, $this->config['signCertPath'], $this->config['signCertPwd']);
    }
}
