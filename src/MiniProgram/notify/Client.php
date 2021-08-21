<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/20 23:44
 * Copyright: php
 */

namespace unionpay\MiniProgram\notify;

use Closure;
use unionpay\Kernel\Client\MiniProgramClient;
use unionpay\Kernel\Traits\MiniProgramNotifyHandle;

/**
 * Class Client
 *
 * @package unionpay\MiniProgram\notify
 */
class Client extends MiniProgramClient
{
    use MiniProgramNotifyHandle;

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
        \call_user_func($closure, $this->getData(), [$this]);
        return $this->toResponse();
    }
}
