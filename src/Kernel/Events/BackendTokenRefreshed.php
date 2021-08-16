<?php

namespace unionpay\Kernel\Events;

use unionpay\MiniProgram\access\BackendToken;

/**
 * Class BackendTokenRefreshed
 *
 * @package unionpay\Kernel\Events
 */
class BackendTokenRefreshed
{
    /**
     * @var BackendToken
     */
    public $backendToken;

    /**
     * @param BackendToken $backendToken
     */
    public function __construct(BackendToken $backendToken)
    {
        $this->backendToken = $backendToken;
    }
}
