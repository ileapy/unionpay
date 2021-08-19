<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/17 16:11
 * Copyright: php
 */

namespace unionpay\MiniProgram\user;

use unionpay\Kernel\Client\MiniProgramClient;

/**
 * Class Card
 *
 * @package unionpay\MiniProgram\user
 */
class Card extends MiniProgramClient
{
    /**
     * @var string
     */
    protected $endpoint = "https://open.95516.com/open/access/1.0/oauth.getCardList";

    /**
     * @var string
     */
    protected $code = "";

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
     * @param string $code 用户授权或静默授权获取的code和openid必传其一
     * @param string $openId 用户唯一标识如果未传递code请确保已调用accessToken后再调用此接口
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
    public function getCardList($code = "", $openId = "", $cardTp = "00", $needSameName = "0", $needPay = "0", $decrypt = true)
    {
        $this->code = $code;
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
        $code = $this->code ?: $this->getCode($this->openId)['code'];
        if (!$code) throw new \Exception("code已失效，请重新授权获取");
        // 获取accessToken和openId
        $access = $this->app->access_token->getToken($code);

        return [
            'appId' => $this->config['appid'],
            'accessToken' => $access['accessToken'],
            'openId' => $this->openId ?: $access['openId'],
            'backendToken' => $this->app->backend_token->getToken()['backendToken'],
            'cardTp' => $this->cardTp,
            'needSameName' => $this->needSameName,
            'needPay' => $this->needPay
        ];
    }
}
