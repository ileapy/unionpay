<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/16 19:09
 * Copyright: php
 */

namespace unionpay\MiniProgram\user;

use Psr\Log\InvalidArgumentException;
use unionpay\Kernel\ServiceContainer;
use unionpay\Kernel\Traits\HasHttpRequests;
use unionpay\Kernel\Traits\InteractsWithCache;

/**
 * Class Mobile
 *
 * @package unionpay\MiniProgram\user
 */
class Mobile
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
    protected $endpointToPostToken = "https://open.95516.com/open/access/1.0/user.mobile";

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $openId = "";

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
     * @param string $openId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception|\Psr\Cache\InvalidArgumentException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function getMobile(string $openId)
    {
        $this->openId = $openId;

        $data = $this->requestToken($this->getCredentials());

        if (!isset($data['mobile'])) throw new \Exception('获取手机号失败，返回值为空');

        return $data;
    }

    /**
     * @param array $credentials
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 10:06
     */
    public function requestToken(array $credentials)
    {
        $response = $this->sendRequest($credentials);
        $result = json_decode($response->getBody()->getContents(), true);

        if (empty($result) || !isset($result['resp']) || $result['resp'] != "00" || !isset($result['params'])) {
            throw new \Exception('Request mobile fail: '.json_encode($result, JSON_UNESCAPED_UNICODE));
        }

        return $result['params'];
    }

    /**
     * Send http request.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendRequest(array $credentials)
    {
        $options = [
            ('GET' === $this->requestMethod) ? 'query' : 'json' => $credentials,
        ];
        return $this->setHttpClient($this->app['http_client'])->request($this->getEndpoint(), $this->requestMethod, $options);
    }

    /**
     * @return string
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 10:04
     */
    public function getEndpoint()
    {
        if (empty($this->endpointToPostToken)) {
            throw new InvalidArgumentException('No endpoint request.');
        }
        return $this->endpointToPostToken;
    }

    /**
     * @param string $openId
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 20:01
     */
    public function getCode(string $openId)
    {
        $cache = $this->getCache();

        $cacheItem = $cache->getItem(md5($openId));

        return $cacheItem->get();
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    protected function getCredentials()
    {
        $code = $this->getCode($this->openId)['code'];
        if (!$code) throw new \Exception("code已失效，请重新授权获取");

        return [
            'appId' => $this->config['appid'],
            'accessToken' => $this->app->access_token->getToken($code)['accessToken'],
            'openId' => $this->openId,
            'backendToken' => $this->app->backend_token->getToken()['backendToken']
        ];
    }
}
