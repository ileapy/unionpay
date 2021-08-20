<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/17 23:12
 * Copyright: php
 */

namespace unionpay\Payment\notify;

use Closure;
use unionpay\Kernel\Client\PaymentClient;
use unionpay\Kernel\Traits\PaymentNotifyHandle;

/**
 * Class Client
 * @package unionpay\Payment\notify
 */
class Client extends PaymentClient
{
    use PaymentNotifyHandle;

    /**
     * @param Closure $closure
     * @param bool $isCheck
     * @return \Symfony\Component\HttpFoundation\Response
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/20 11:49
     * @throws \Exception
     */
    public function unit(Closure $closure, $isCheck = true)
    {
        $this->isCheck = $isCheck;
        $this->strict(
            \call_user_func($closure, $this->getData(), [$this, 'fail'])
        );
        return $this->toResponse();
    }
}
