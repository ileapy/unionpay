<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/15 23:13
 * Copyright: php
 */

namespace unionpay\Kernel\Contracts;

use Psr\Http\Message\RequestInterface;

/**
 * Interface BackendTokenInterface
 *
 * @package unionpay\Kernel\Contracts
 */
interface BackendTokenInterface
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
