<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/20 23:44
 * Copyright: php
 */

namespace unionpay\MiniProgram\qual;

use unionpay\Kernel\Client\MiniProgramClient;
use unionpay\Kernel\Support\Str;

/**
 * Class Client
 * @package unionpay\MiniProgram\qual
 */
class Client extends MiniProgramClient
{
    /**
     * 抽奖资格赠送
     * @param array $params 参数
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/21 0:48
     */
    public function given($params = [])
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/qual.send";

        $base = [
            'appId' => $this->config['appid'],
            'transNumber' => date('YmdHis').rand(1000,9999),
            'nonceStr' => Str::nonceStr(),
            'timestamp' => time(),
            'qualType' => 'open_id '
        ];

        $params = array_replace_recursive($base, $params);

        if (!isset($params['qualNum']) || !isset($params['qualValue']) || !isset($params['count']) || !isset($params['startDate']) || !isset($params['endDate']))
            throw new \Exception('资格池编号[qualNum]和资格值[qualValue]和资格增加次数[count]和资格起始时间[startDate]和资格结束时间[endDate]需上报');

        // 加密
        if (isset($params['qualValue']) && !empty($params['qualValue'])) $params['qualValue'] = $this->app->crypto->encrypt($params['qualValue']);
        if (isset($params['count']) && !empty($params['count'])) $params['count'] = $this->app->crypto->encrypt($params['count']);

        $params['signature'] = $this->app->crypto->sign($params);

        return $this->requestToken($params);
    }

    /**
     * 抽奖资格查询
     * @param array $params 参数
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/21 0:48
     */
    public function query($params = [])
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/qual.select";

        $base = [
            'appId' => $this->config['appid'],
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'qualType' => 'open_id'
        ];

        $params = array_replace_recursive($base, $params);

        if (!isset($params['qualNum']) || !isset($params['qualValue']))
            throw new \Exception('资格池编号[qualNum]和资格值[qualValue]需上报');

        // 加密
        if (isset($params['qualValue']) && !empty($params['qualValue'])) $params['qualValue'] = $this->app->crypto->encrypt($params['qualValue']);

        return $this->requestToken($params);
    }

    /**
     * 抽奖
     * @param array $params 参数
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/21 0:48
     */
    public function lotto($params = [])
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/qual.reduce";

        $base = [
            'appId' => $this->config['appid'],
            'transNumber' => date('YmdHis').rand(1000,9999),
            'nonceStr' => Str::nonceStr(),
            'timestamp' => time(),
            'qualType' => 'open_id '
        ];

        $params = array_replace_recursive($base, $params);

        if (!isset($params['qualNum']) || !isset($params['qualValue']) || !isset($params['activityNumber']))
            throw new \Exception('资格池编号[qualNum]和资格值[qualValue]和资格增加次数[activityNumber]需上报');

        // 加密
        if (isset($params['qualValue']) && !empty($params['qualValue'])) $params['qualValue'] = $this->app->crypto->encrypt($params['qualValue']);
        if (isset($params['certId']) && !empty($params['certId'])) $params['certId'] = $this->app->crypto->encrypt($params['certId']);

        $params['signature'] = $this->app->crypto->sign($params);

        return $this->requestToken($params);
    }

    /**
     * 直接抽奖
     * @param array $params 参数
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/21 0:48
     */
    public function directLotto($params = [])
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/maktg.draw";

        $base = [
            'appId' => $this->config['appid'],
            'transSeqId' => date('YmdHis').rand(1000,9999),
            'transTs' => date('Ymd'),
            'nonceStr' => Str::nonceStr(),
            'timestamp' => time(),
            'acctEntityTp' => '01'
        ];

        $params = array_replace_recursive($base, $params);

        if (!isset($params['activityNo']))
            throw new \Exception('活动编号[activityNo]需上报');

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
}
