<?php


namespace unionpay\Kernel\Events;

use unionpay\MiniProgram\access\AccessToken;

/**
 * Class AccessTokenRefreshed
 *
 * @package unionpay\Kernel\Events
 */
class AccessTokenRefreshed
{
    /**
     * @var AccessToken
     */
    public $accessToken;

    /**
     * @param AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }
}
