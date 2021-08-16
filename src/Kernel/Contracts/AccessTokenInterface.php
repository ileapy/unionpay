<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/15 23:14
 * Copyright: php
 */

namespace unionpay\Kernel\Contracts;

use Psr\Http\Message\RequestInterface;

/**
 * Interface AccessTokenInterface
 *
 * @package unionpay\Kernel\Contracts
 */
interface AccessTokenInterface
{
    /**
     * @return array
     */
    public function getToken();

    /**
     * @return AccessTokenInterface
     */
    public function getRefreshedToken();
}
