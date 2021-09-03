<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/17 23:12
 * Copyright: php
 */

namespace unionpay\Payment\file;

use Exception;
use unionpay\Kernel\Client\PaymentClient;
use unionpay\Kernel\Support\AcpService;

/**
 * Class Client
 *
 * @package unionpay\Payment\file
 */
class Client extends PaymentClient
{
    /**
     * 此接口咱不可用
     * 银联加密公钥更新查询接口
     * @throws Exception|\GuzzleHttp\Exception\GuzzleException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/20 19:39
     */
    public function updatePublicKey($params)
    {
        $this->endpoint = "backTransReq.do";

        $base = [
            // 产品类型
            'bizType'          =>      '000201',
            // 订单发送时间，格式为YYYYMMDDhhmmss，取北京时间
            'txnTime'        =>        date('YmdHis'),
            // 交易类型
            'txnType'        =>        '95',
            // 交易子类
            'txnSubType'     =>        '00',
            // 商户代码，请改自己的商户号
            'merId'          =>        $this->config['merId']
        ];

        $data = array_replace_recursive($this->config['payConfig'], $base, $params);

        // 必填项校验
        if (!isset($data['orderId']))
            throw new \Exception("商户订单号[orderId]必传，自定义");

        // 签名
        $this->app->signature->sign($data, $this->config['signCertPath'], $this->config['signCertPwd']);

        // 数据返回
        return $this->requestToken($data);
    }

    /**
     * 文件传输类交易接口
     * @param array $params
     * @return false|string[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/20 19:58
     */
    public function download($params)
    {
        $this->endpoint = ""; // 文件下载直接填空

        $base = [
            // 产品类型
            'bizType'          =>      '000000',
            // 订单发送时间，格式为YYYYMMDDhhmmss，取北京时间
            'txnTime'        =>        date('YmdHis'),
            // 交易类型
            'txnType'        =>        '76',
            // 交易子类
            'txnSubType'     =>        '01',
            // 商户代码，请改自己的商户号
            'merId'          =>        $this->config['merId'],
            // 文件类型
            'fileType' => '00',
        ];

        $data = array_replace_recursive($this->config['payConfig'], $base, $params);

        // 必填项校验
        if (!isset($data['settleDate']))
            throw new \Exception("清算日期[settleDate]必传，格式为MMDD");

        // 签名
        $this->app->signature->sign($data, $this->config['signCertPath'], $this->config['signCertPwd']);

        // 数据返回
        return $this->requestToken($data);
    }

    /**
     * 保存文件
     * @param array $params download返回的数据
     * @param string $filePath 文件保存地址
     * @return array|false
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/20 20:14
     * @throws Exception
     */
    public function save($params, $filePath)
    {
        // 文件不存在
        if ($params['respCode'] == "98")
            throw new Exception('文件不存在。', 400);
        elseif ($params['respCode'] != "00")
            throw new Exception('获取失败。', 400);

        // 成功返回文件名称 失败返回false
        if (AcpService::decodeFileContent($params, $filePath))
        {
            $fileName =  isset($params['fileName']) && !empty($params['fileName']) ? $filePath . $params['fileName'] : $filePath . $params['merId'] . '_' . $params['batchNo'] . '_' . $params['txnTime'] . '.txt';
            return compact('fileName');
        }
        return false;
    }
}
