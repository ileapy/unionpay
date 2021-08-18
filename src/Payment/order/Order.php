<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/17 23:14
 * Copyright: php
 */

namespace unionpay\Payment\order;

use unionpay\Kernel\ServiceContainer;
use unionpay\Kernel\Support\AcpService;
use unionpay\Kernel\Traits\HasHttpRequests;
use unionpay\Kernel\Traits\InteractsWithCache;

/**
 * Class Order
 *
 * @package unionpay\Payment\order
 */
class Order
{
    use HasHttpRequests;
    use InteractsWithCache;

    /**
     * @var ServiceContainer
     */
    protected $app;

    /**
     * @var string
     */
    protected $requestMethod = 'POST';

    /**
     * @var string
     */
    protected $endpoint = "https://gateway.95516.com/gateway/api/appTransReq.do";

    /**
     * 配置
     * @var array
     */
    protected $config = [];

    /**
     * 支付
     * @var array
     */
    private $params = [];

    /**
     * AccessToken constructor.
     *
     * @param ServiceContainer $app
     */
    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
        $this->config = $app['config']->toArray();
    }

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

        $data = $this->requestToken($this->signCredentialsData());

        if (empty($data)) throw new \Exception("未获取到数据");

        $validate = AcpService::validate($data,'', $this->config['cert']['middleCertPath'], $this->config['cert']['rootCertPath'], $this->config['debug']);

        if (!$validate && isset($data['respMsg'])) throw new \Exception($data['respMsg']);

        return $data;
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

        AcpService::sign($data, $this->config['signCertPath'], $this->config['signCertPwd']);

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
