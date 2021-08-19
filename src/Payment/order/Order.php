<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/17 23:14
 * Copyright: php
 */

namespace unionpay\Payment\order;

use unionpay\Kernel\Client\PaymentClient;

/**
 * Class Order
 *
 * @package unionpay\Payment\order
 */
class Order extends PaymentClient
{
    /**
     * @var string
     */
    protected $endpoint = "https://gateway.95516.com/gateway/api/appTransReq.do";

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
    public function pay($params)
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

        if (!isset($data['txnAmt']) || !isset($data['orderId']))
            throw new \Exception("商户订单号[txnAmt]和订单金额[orderId]必传");

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
            'txnType'        =>        '01',
            // 交易子类
            'txnSubType'     =>        '01',
            // 商户代码，请改自己的商户号
            'merId'          =>        $this->config['merId']
        ];
        return array_replace_recursive($this->config['payConfig'], $base, $this->params);
    }
}