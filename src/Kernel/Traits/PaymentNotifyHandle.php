<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/20 9:33
 * Copyright: php
 */

namespace unionpay\Kernel\Traits;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use unionpay\Kernel\Support\Str;

/**
 * Class PaymentNotifyHandle
 *
 * @package unionpay\Kernel\Traits
 */
trait PaymentNotifyHandle
{
    /**
     * @var string
     */
    protected static $SUCCESS = 'success';

    /**
     * @var string
     */
    protected static $FAIL = 'fail';

    /**
     * @var string
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $isSuccess = true;

    /**
     * 是否验签
     * @var bool
     */
    public $isCheck = true;

    /**
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/20 9:34
     */
    protected function toResponse()
    {
        return new Response($this->isSuccess ? self::$SUCCESS : self::$FAIL);
    }

    /**
     * @return array|string
     * @throws Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/20 9:37
     */
    protected function getData()
    {
        if (!empty($this->data)) return $this->data;

        try {
            $param_str = strval($this->app['request']->getContent());
            parse_str($param_str,$data);
        }catch (Exception $e)
        {
            throw new Exception('Invalid request.', 400);
        }

        if (!is_array($data) || empty($data)) {
            throw new Exception('Invalid request.', 400);
        }

        if ($this->isCheck)
            if (!$this->app->signature->validate($data))
                throw new Exception('Invalid signature.', 400);

        return $this->data = $data;
    }

    /**
     * @param mixed $result
     */
    protected function strict($result)
    {
        $this->isSuccess = true !== $result;
    }
}
