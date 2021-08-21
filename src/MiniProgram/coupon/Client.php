<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/20 23:45
 * Copyright: php
 */

namespace unionpay\MiniProgram\coupon;

use unionpay\Kernel\Client\MiniProgramClient;
use unionpay\Kernel\Support\Str;

/**
 * Class Client
 *
 * @package unionpay\MiniProgram\coupon
 */
class Client extends MiniProgramClient
{
    /**
     * 赠送优惠券
     * @param $params
     * @return mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/21 0:48
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function given($params)
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/coupon.download";

        $base = [
            'appId' => $this->config['appid'],
            'transTs' => date("Ymd"),
            'nonceStr' => Str::nonceStr(),
            'timestamp' => time(),
            'transSeqId' => date('YmdHis').rand(1000,9999),
            // 默认送一张
            'couponNum' => 1
        ];
        $params = array_replace_recursive($base, $params);

        // 必填项验证
        if (!isset($params['couponId']) || !isset($params['acctEntityTp'])) throw new \Exception('优惠券id[couponId]或营销活动配置的赠送维度[acctEntityTp]未上报');
        if ($params['acctEntityTp'] == '02' && !isset($params['cardNo'])) throw new \Exception('02 -卡号 赠送维度为卡号时，则 cardNo 必填');
        elseif ($params['acctEntityTp'] == '03' && (!isset($params['cardNo']) && !isset($params['mobile']) && !isset($params['openId']))) throw new \Exception('03 -用户 赠送维度为用户时，则 openId ，mobile , cardNo 三选一上送');
        elseif($params['acctEntityTp'] != '02' && $params['acctEntityTp'] != '03') throw new \Exception('赠送维度不正确');
        // 加密
        if (isset($params['mobile']) && !empty($params['mobile'])) $params['mobile'] = $this->app->crypto->encrypt($params['mobile']);
        if (isset($params['cardNo']) && !empty($params['cardNo'])) $params['cardNo'] = $this->app->crypto->encrypt($params['cardNo']);

        $params['signature'] = $this->app->crypto->sign($params);

        return $this->requestToken($params);
    }

    /**
     * 赠送优惠券结果查询
     * @param $params
     * @return mixed
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/21 0:51
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function query($params)
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/coupon.query";

        $base = [
            'appId' => $this->config['appid'],
            'transTs' => date("Ymd"),
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'transSeqId' => date('YmdHis').rand(1000,9999),
        ];
        $params = array_replace_recursive($base, $params);

        // 必填项验证
        if (!isset($params['origTransSeqId']) || !isset($params['origTransTs']))
            throw new \Exception('原交易流水号（赠送优惠券时流水号）[origTransSeqId]或原请求日期（赠送优惠券日期）[origTransTs]未上报');

        return $this->requestToken($params);
    }

    /**
     * 优惠券活动剩余名额查询
     * @param $params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException*@throws \Exception
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/21 0:53
     */
    public function quota($params)
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/activity.quota";

        $base = [
            'appId' => $this->config['appid'],
            'transSeqId' => date('YmdHis').rand(1000,9999),
            'backendToken' => $this->app->backend_token->getToken()['backendToken']
        ];
        $params = array_replace_recursive($base, $params);

        // 必填项验证
        if (!isset($params['activityNo']) || !isset($params['activityType']))
            throw new \Exception('活动编号[activityNo]或活动类型: 1 .u点全场立减（同原线上立减）、 2 .u点全场券、 3 .线下立减、 4 .折扣券、 5 .代金券、 6 .满抵券、 7 .随机立减券、 8 .凭证券、 9 .提货券、 10 .送货券、 11 .精准营销展示券、 12 .单品券、 13 .单品立减[activityType]未上报');

        return $this->requestToken($params);
    }
}
