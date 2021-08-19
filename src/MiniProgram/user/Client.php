<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/19 22:34
 * Copyright: php
 */

namespace unionpay\MiniProgram\user;

use unionpay\Kernel\Client\MiniProgramClient;

/**
 * Class Client
 *
 * @package unionpay\MiniProgram\user
 */
class Client extends MiniProgramClient
{
    /**
     * 获取手机号
     * @param string $code 用户授权或静默授权获取的code和openid必传其一
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @param bool $decrypt 是否解密返回
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function mobile($code = "", $openId = "", $decrypt = true)
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/user.mobile";

        $code = $code ?: $this->getCode($openId)['code'];
        if (!$code) throw new \Exception("code已失效，请重新授权获取");
        // 获取accessToken和openId
        $access = $this->app->access_token->getToken($code);

        $params = [
            'appId' => $this->config['appid'],
            'accessToken' => $access['accessToken'],
            'openId' => $openId ?: $access['openId'],
            'backendToken' => $this->app->backend_token->getToken()['backendToken']
        ];

        $data = $this->requestToken($params);

        if (!isset($data['mobile'])) throw new \Exception('获取手机号失败，返回值为空');

        // 解密返回
        if ($decrypt)
            foreach ($data as $k => $v)
                if ($v)
                    $data[$k] = $this->app->crypto->decrypt($v);

        return $data;
    }

    /**
     * 获取基础信息
     * @param string $code 用户授权或静默授权获取的code和openid必传其一
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @param bool $decrypt 是否解密返回
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function auth($code = "", $openId = "", $decrypt = true)
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/user.auth";

        $code = $code ?: $this->getCode($openId)['code'];
        if (!$code) throw new \Exception("code已失效，请重新授权获取");

        // 获取accessToken和openId
        $access = $this->app->access_token->getToken($code);

        $params = [
            'appId' => $this->config['appid'],
            'accessToken' => $access['accessToken'],
            'openId' => $openId ?: $access['openId'],
            'backendToken' => $this->app->backend_token->getToken()['backendToken']
        ];

        $data = $this->requestToken($params);

        // 解密返回
        if ($decrypt)
            foreach ($data as $k => $v)
                if ($v)
                    $data[$k] = $this->app->crypto->decrypt($data[$k]);

        return $data;
    }

    /**
     * 获取银行卡列表
     * @param string $code 用户授权或静默授权获取的code和openid必传其一
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @param string $cardTp 需要获取的卡列表类型：（ 00 ：全部， 01 ：借记卡， 02 ：贷记卡， 03 ：准贷记卡， 04 ：借贷合一卡）
     * @param string $needSameName 是否要求卡同名：（ 0 :不需要，同名卡和非同名卡都返回， 1 :需要，只返回同名卡）
     * @param string $needPay 是否要求开通支付的卡（ 0 ：不需要，支付卡和非支付卡都返回， 1 ：需要，只返回开通支付支付卡）
     * @param bool $decrypt 是否解密返回
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function card($code = "", $openId = "", $cardTp = "00", $needSameName = "0", $needPay = "0", $decrypt = true)
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/oauth.getCardList";

        $code = $code ?: $this->getCode($openId)['code'];
        if (!$code) throw new \Exception("code已失效，请重新授权获取");
        // 获取accessToken和openId
        $access = $this->app->access_token->getToken($code);

        $params = [
            'appId' => $this->config['appid'],
            'accessToken' => $access['accessToken'],
            'openId' => $openId ?: $access['openId'],
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'cardTp' => $cardTp,
            'needSameName' => $needSameName,
            'needPay' => $needPay
        ];

        $data = $this->requestToken($params);

        // 解密返回
        if ($decrypt && !empty($data) && isset($data['cardList']))
            foreach ($data['cardList'] as &$card)
                foreach ($card as $k => $v)
                    if ($v)
                        $card[$k] = $this->app->crypto->decrypt($v);

        return $data;
    }

    /**
     * 获取银行卡token
     * @param string $code 用户授权或静默授权获取的code和openid必传其一
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @param bool $decrypt 是否解密返回
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function cardToken($code = "", $openId = "", $decrypt = true)
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/user.checkedCard";

        $code = $code ?: $this->getCode($openId)['code'];
        if (!$code) throw new \Exception("code已失效，请重新授权获取");
        // 获取accessToken和openId
        $access = $this->app->access_token->getToken($code);

        $params = [
            'appId' => $this->config['appid'],
            'accessToken' => $access['accessToken'],
            'openId' => $openId ?: $access['openId'],
            'backendToken' => $this->app->backend_token->getToken()['backendToken']
        ];

        $data = $this->requestToken($params);

        // 解密返回
        if ($decrypt)
            foreach ($data as $k => $v)
                if ($v)
                    $data[$k] = $this->app->crypto->decrypt($v);

        return $data;
    }

    /**
     * 获取用户状态
     * @param string $openId 用户唯一标识，通过获取授权访问令牌获取
     * @param string $markTime 标记时间 (yyyy-MM-dd hh:mm:ss) ,如要上送此字段,必须按照规定格式
     * @param bool $decrypt 是否解密返回
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function userStatus($openId, $markTime ="", $decrypt = true)
    {
        $this->endpoint = "https://open.95516.com/open/access/1.0/user.status";

        $params = [
            'appId' => $this->config['appid'],
            'openId' => $openId,
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
        ];
        if ($markTime) $params['markTime'] = $markTime;

        $data = $this->requestToken($params);

        // 解密返回
        if ($decrypt)
            foreach ($data as $k => $v)
                if ($v)
                    $data[$k] = $this->app->crypto->decrypt($v);

        return $data;
    }

    /**
     * 通过openid获取code
     * @param string $openId
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 20:01
     */
    public function getCode($openId)
    {
        $cache = $this->getCache();

        $cacheItem = $cache->getItem(md5($openId));

        return $cacheItem->get();
    }
}
