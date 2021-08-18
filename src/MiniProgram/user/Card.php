<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/17 16:11
 * Copyright: php
 */

namespace unionpay\MiniProgram\user;

use unionpay\Kernel\ServiceContainer;
use unionpay\Kernel\Traits\HasHttpRequests;
use unionpay\Kernel\Traits\InteractsWithCache;

/**
 * Class Card
 *
 * @package unionpay\MiniProgram\user
 */
class Card
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
    protected $endpoint = "https://open.95516.com/open/access/1.0/oauth.getCardList";

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $openId = "";

    /**
     * @var string
     */
    private $cardTp;

    /**
     * @var string
     */
    private $needSameName;

    /**
     * @var string
     */
    private $needPay;

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
     * @param string $openId openid
     * @param string $cardTp 需要获取的卡列表类型：（ 00 ：全部， 01 ：借记卡， 02 ：贷记卡， 03 ：准贷记卡， 04 ：借贷合一卡）
     * @param string $needSameName 是否要求卡同名：（ 0 :不需要，同名卡和非同名卡都返回， 1 :需要，只返回同名卡）
     * @param string $needPay 是否要求开通支付的卡（ 0 ：不需要，支付卡和非支付卡都返回， 1 ：需要，只返回开通支付支付卡）
     * @param bool $decrypt 是否解密返回
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 19:23
     */
    public function getCardList($openId, $cardTp = "00", $needSameName = "0", $needPay = "0", $decrypt = true)
    {
        $this->openId = $openId;
        $this->cardTp = $cardTp;
        $this->needSameName = $needSameName;
        $this->needPay = $needPay;

        $data = $this->requestToken($this->getCredentials());

        // 解密返回
        if ($decrypt && !empty($data) && isset($data['cardList']))
            foreach ($data['cardList'] as &$card)
                foreach ($card as $k => $v)
                    if ($v)
                        $card[$k] = $this->app->crypto->decrypt($v);

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
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'cardTp' => $this->cardTp,
            'needSameName' => $this->needSameName,
            'needPay' => $this->needPay
        ];
    }
}
