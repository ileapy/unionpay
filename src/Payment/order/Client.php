<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/19 21:35
 * Copyright: php
 */

namespace unionpay\Payment\order;

use unionpay\Kernel\Client\PaymentClient;

/**
 * Class Client
 *
 * @package unionpay\Payment\order
 */
class Client extends PaymentClient
{
    /**
     * 支付
     * @param $params
     * @return mixed
     * @throws \Exception|\GuzzleHttp\Exception\GuzzleException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function pay($params, $isCheck = true)
    {
        // 地址
        $this->endpoint = "appTransReq.do";

        // 固定数据
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

        // 合并
        $data = array_replace_recursive($this->config['payConfig'], $base, $params);

        // 必填项校验
        if (!isset($data['orderId']) || !isset($data['txnAmt']))
            throw new \Exception("商户订单号[orderId]和订单金额[txnAmt]必传");

        // 签名
        $this->app->signature->sign($data);

        // 数据返回
        return $this->requestToken($data);
    }

    /**
     * 订单撤销
     * @param $params
     * @return mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/19 22:14
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cancel($params)
    {
        $this->endpoint = "backTransReq.do";

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

        $data = array_replace_recursive($this->config['payConfig'], $base, $params);

        // 必填项校验
        if (!isset($data['txnAmt']) || !isset($data['orderId']) || !isset($data['origQryId']))
            throw new \Exception("商户订单号(重新生成，相当于退款单号)[orderId]和订单金额（和原订单金额一样）[txnAmt]和原交易查询流水号（支付成功后返回的）[origQryId]必传");

        // 签名
        $this->app->signature->sign($data);

        // 数据返回
        return $this->requestToken($data);
    }

    /**
     * 订单退货
     * @param $params
     * @return mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/19 22:15
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refund($params)
    {
        $this->endpoint = "backTransReq.do";

        $base = [
            // 产品类型
            'bizType'          =>      '000201',
            // 订单发送时间，格式为YYYYMMDDhhmmss，取北京时间
            'txnTime'        =>        date('YmdHis'),
            // 交易类型
            'txnType'        =>        '04',
            // 交易子类
            'txnSubType'     =>        '00',
            // 商户代码，请改自己的商户号
            'merId'          =>        $this->config['merId']
        ];

        $data = array_replace_recursive($this->config['payConfig'], $base, $params);

        // 必填项校验
        if (!isset($data['txnAmt']) || !isset($data['orderId']) || !isset($data['origQryId']))
            throw new \Exception("商户订单号(重新生成，相当于退款单号)[orderId]和订单金额（退货总金额需要小于等于原消费）[txnAmt]和原交易查询流水号（支付成功后返回的）[origQryId]必传");

        // 签名
        $this->app->signature->sign($data);

        // 数据返回
        return $this->requestToken($data);
    }

    /**
     * 订单查询
     * @param $params
     * @return mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/19 22:17
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function query($params)
    {
        $this->endpoint = "queryTrans.do";

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
        $data = array_replace_recursive($this->config['payConfig'], $base, $params);

        // 必填项校验
        if (!isset($data['orderId']))
            throw new \Exception("商户订单号[orderId]必传");

        // 签名
        $this->app->signature->sign($data);

        // 数据返回
        return $this->requestToken($data);
    }
}
