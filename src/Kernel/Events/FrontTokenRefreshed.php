<?php

namespace unionpay\Kernel\Events;

use unionpay\MiniProgram\access\FrontToken;

/**
 * Class FrontTokenRefreshed
 *
 * @package unionpay\Kernel\Events
 */
class FrontTokenRefreshed
{
    /**
     * @var FrontToken
     */
    public $frontToken;

    /**
     * @param FrontToken $frontToken
     */
    public function __construct(FrontToken $frontToken)
    {
        $this->frontToken = $frontToken;
    }
}
