<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/17 16:13
 * Copyright: php
 */

namespace unionpay\MiniProgram\user;

use unionpay\Kernel\Client\MiniProgramClient;

/**
 * Class CardToken
 *
 * @package unionpay\MiniProgram\user
 */
class CardToken extends MiniProgramClient
{
    /**
     * @var string
     */
    protected $endpoint = "https://open.95516.com/open/access/1.0/user.checkedCard";

    /**
     * @var
     */
    protected $code = "";

    /**
     * @var string
     */
    protected $openId = "";

    /**
     * @param string $code 用户授权或静默授权获取的code和openid必传其一
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
     * @param bool $decrypt 是否解密返回
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function getCardToken($code = "", $openId = "", $decrypt = true)
    {
        $this->code = $code;
        $this->openId = $openId;

        $data = $this->requestToken($this->getCredentials());

        // 解密返回
        // 解密返回
        if ($decrypt)
            foreach ($data as $k => $v)
                if ($v)
                    $data[$k] = $this->app->crypto->decrypt($data[$k]);

        return $data;
    }

    /**
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

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    protected function getCredentials()
    {
        $code = $this->code ?: $this->getCode($this->openId)['code'];
        if (!$code) throw new \Exception("code已失效，请重新授权获取");
        // 获取accessToken和openId
        $access = $this->app->access_token->getToken($code);

        return [
            'appId' => $this->config['appid'],
            'accessToken' => $access['accessToken'],
            'openId' => $this->openId ?: $access['openId'],
            'backendToken' => $this->app->backend_token->getToken()['backendToken']
        ];
    }
}
