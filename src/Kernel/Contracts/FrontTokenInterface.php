<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/15 23:13
 * Copyright: php
 */

namespace unionpay\Kernel\Contracts;

use Psr\Http\Message\RequestInterface;

/**
 * Interface FrontTokenInterface
 *
 * @package unionpay\Kernel\Contracts
 */
interface FrontTokenInterface
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
