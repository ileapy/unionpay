<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/20 23:45
 * Copyright: php
 */

namespace unionpay\MiniProgram\redpack;

use unionpay\Kernel\Client\MiniProgramClient;
use unionpay\Kernel\Support\Str;

/**
 * Class Client
 *
 * @package unionpay\MiniProgram\redpack
 */
class Client extends MiniProgramClient
{
    /**
     * 赠送专享红包
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @param array $params 更多参数
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/21 0:48
     */
    public function given($openId = "", $params = [])
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/point.acquire";

        $base = [
            'appId' => $this->config['appid'],
            'transSeqId' => date('YmdHis').rand(1000,9999),
            'transTs' => date('YmdHis'),
            'openId' => $openId,
            'nonceStr' => Str::nonceStr(),
            'timestamp' => time(),
            'acctEntityTp' => '01'
        ];

        $params = array_replace_recursive($base, $params);

        if (!isset($params['insAcctId']) || !isset($params['pointId']) || !isset($params['pointAt']) || !isset($params['busiInfo']) || !isset($params['transDigest']))
            throw new \Exception('机构账户代码[insAcctId]和积分id[pointId]和积分额[pointAt]和业务信息[busiInfo]和交易摘要[transDigest]需上报');

        if ($params['acctEntityTp'] == '01' && !isset($params['mobile'])) throw new \Exception('账号主体类型为01时手机号需上报');
        elseif ($params['acctEntityTp'] == '02' && !isset($params['cardNo'])) throw new \Exception('账号主体类型为02时卡号需上报');
        elseif($params['acctEntityTp'] == '03' && !isset($params['openId'])) throw new \Exception('账号主体类型为03时用户唯一标识需上报');
        elseif($params['acctEntityTp'] != '03' && $params['acctEntityTp'] != '01' && $params['acctEntityTp'] != '02') throw new \Exception('账户主体类型不正确');

        // 加密
        if (isset($params['mobile']) && !empty($params['mobile'])) $params['mobile'] = $this->app->crypto->encrypt($params['mobile']);
        if (isset($params['cardNo']) && !empty($params['cardNo'])) $params['cardNo'] = $this->app->crypto->encrypt($params['cardNo']);

        $params['signature'] = $this->app->crypto->sign($params);

        return $this->requestToken($params);
    }

    /**
     * 机构账户（红包）余额查询
     * @param array $params 更多参数
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/21 0:48
     */
    public function orgQuery($params = [])
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/red.packet.select";

        $base = [
            'appId' => $this->config['appid'],
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'transNumber' => date('YmdHis').rand(1000,9999),
            'transTm' => date('YmdHis'),
            'transDt' => date('Ymd'),
        ];

        $params = array_replace_recursive($base, $params);

        if (!isset($params['insAcctId']))
            throw new \Exception('机构账户代码[insAcctId]需上报');

        return $this->requestToken($params);
    }

    /**
     * 专享红包余额查询
     * @param array $params 更多参数
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/21 0:48
     */
    public function excQuery($params = [])
    {
        $this->endpoint = "1.0/red.packet.select";

        $base = [
            'appId' => $this->config['appid'],
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'transSeq' => date('YmdHis').rand(1000,9999),
            'transTs' => date('YmdHis'),
            'accessId' => 'UP',
            'acctEntityTp' => '01'
        ];

        $params = array_replace_recursive($base, $params);

        if (!isset($params['pointId']) || !isset($params['acctEntityId']) || !isset($params['remark']))
            throw new \Exception('专享活动活动id[pointId]和账户主体值[acctEntityId]和备注[remark]需上报');

        if (isset($params['acctEntityId']) && !empty($params['acctEntityId'])) $params['acctEntityId'] = $this->app->crypto->encrypt($params['acctEntityId']);

        return $this->requestToken($params);
    }
}
