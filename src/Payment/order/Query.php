<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/17 23:12
 * Copyright: php
 */

namespace unionpay\Payment\order;

use unionpay\Kernel\Client\MiniProgramClient;
use unionpay\Kernel\Support\AcpService;

/**
 * Class Query
 *
 * @package unionpay\Payment\order
 */
class Query extends MiniProgramClient
{
    /**
     * @var string
     */
    protected $endpoint = "https://gateway.95516.com/gateway/api/queryTrans.do";

    /**
     * 支付
     * @var array
     */
    private $params = [];

    /**
     * 交易状态查询接口
     * 注意：订单发送时间[txnTime]必填 被查询交易的交易时间，默认当前时间
     * @param $params
     * @return mixed
     * @throws \Exception|\GuzzleHttp\Exception\GuzzleException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function query($params)
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

        if (!isset($data['orderId']))
            throw new \Exception("商户订单号[orderId]必传");

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
            'bizType'          =>      '000000',
            // 订单发送时间，格式为YYYYMMDDhhmmss，取北京时间
            'txnTime'        =>        date('YmdHis'),
            // 交易类型
            'txnType'        =>        '00',
            // 交易子类
            'txnSubType'     =>        '00',
            // 商户代码，请改自己的商户号
            'merId'          =>        $this->config['merId']
        ];
        return array_replace_recursive($this->config['payConfig'], $base, $this->params);
    }
}
