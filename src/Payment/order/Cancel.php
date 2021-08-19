<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/17 23:12
 * Copyright: php
 */

namespace unionpay\Payment\order;

use unionpay\Kernel\Client\PaymentClient;
use unionpay\Kernel\Support\AcpService;

/**
 * Class Cancel
 *
 * @package unionpay\Payment\cancel
 */
class Cancel extends PaymentClient
{
    /**
     * @var string
     */
    protected $endpoint = "https://gateway.95516.com/gateway/api/backTransReq.do";

    /**
     * 支付
     * @var array
     */
    private $params = [];

    /**
     * @param $params
     * @return mixed
     * @throws \Exception|\GuzzleHttp\Exception\GuzzleException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function cancel($params)
    {
        $this->params = $params;

        return $this->requestToken($this->signCredentialsData());
    }

    /**
     * @return array
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/18 11:51
     * @throws \Exception
     */
    protected function signCredentialsData()
    {
        $data = $this->getCredentials();

        if (!isset($data['txnAmt']) || !isset($data['orderId']) || !isset($data['origQryId']))
            throw new \Exception("商户订单号(重新生成，相当于退款单号)[orderId]和订单金额（和原订单金额一样）[txnAmt]和原交易查询流水号（支付成功后返回的）[origQryId]必传");

        $this->app->signature->sign($data, $this->config['signCertPath'], $this->config['signCertPwd']);

        return $data;
    }

    /**
     * @return array
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    protected function getCredentials()
    {
        $base = [
            // 产品类型
            'bizType'          =>      '000201',
            // 订单发送时间，格式为YYYYMMDDhhmmss，取北京时间
            'txnTime'        =>        date('YmdHis'),
            // 交易类型
            'txnType'        =>        '31',
            // 交易子类
            'txnSubType'     =>        '00',
            // 商户代码，请改自己的商户号
            'merId'          =>        $this->config['merId']
        ];
        return array_replace_recursive($this->config['payConfig'], $base, $this->params);
    }
}
