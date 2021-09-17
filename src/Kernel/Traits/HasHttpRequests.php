<?php
/**
 * User: cfn <cfn@leapy.cn>
 * Datetime: 2021/8/16 9:03
 * Copyright: php
 */

namespace unionpay\Kernel\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use unionpay\Kernel\Support\AcpService;

trait HasHttpRequests
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;

    /**
     * @var array
     */
    protected $middlewares = [];

    /**
     * @var \GuzzleHttp\HandlerStack
     */
    protected $handlerStack;

    /**
     * @var array
     */
    protected static $defaults = [
        'curl' => [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ],
    ];

    /**
     * Set guzzle default settings.
     *
     * @param array $defaults
     */
    protected static function setDefaultOptions($defaults = [])
    {
        self::$defaults = $defaults;
    }

    /**
     * Return current guzzle default settings.
     */
    protected static function getDefaultOptions()
    {
        return self::$defaults;
    }

    /**
     * Set GuzzleHttp\Client.
     *
     * @return $this
     */
    protected function setHttpClient(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * Return GuzzleHttp\ClientInterface instance.
     */
    protected function getHttpClient()
    {
        if (!($this->httpClient instanceof ClientInterface)) {
            if (property_exists($this, 'app') && $this->app['http_client']) {
                $this->httpClient = $this->app['http_client'];
            } else {
                $this->httpClient = new Client(['handler' => HandlerStack::create($this->getGuzzleHandler())]);
            }
        }

        return $this->httpClient;
    }

    /**
     * Add a middleware.
     *
     * @param callable $middleware
     * @param string|null $name
     *
     * @return $this
     */
    protected function pushMiddleware(callable $middleware, $name = null)
    {
        if (!is_null($name)) {
            $this->middlewares[$name] = $middleware;
        } else {
            array_push($this->middlewares, $middleware);
        }

        return $this;
    }

    /**
     * Return all middlewares.
     */
    protected function getMiddlewares()
    {
        return $this->middlewares;
    }

    /**
     * Make a request.
     *
     * @param string $url
     * @param string $method
     * @param array  $options
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request($url, $method = 'GET', $options = [])
    {
        $method = strtoupper($method);

        $options = array_merge(self::$defaults, $options, ['handler' => $this->getHandlerStack()]);

        $options = $this->fixJsonIssue($options);

        if (property_exists($this, 'baseUri') && !is_null($this->baseUri)) {
            $options['base_uri'] = $this->baseUri;
        }

        $response = $this->getHttpClient()->request($method, $url, $options);
        $response->getBody()->rewind();

        return $response;
    }

    /**
     * @return $this
     */
    protected function setHandlerStack(HandlerStack $handlerStack)
    {
        $this->handlerStack = $handlerStack;

        return $this;
    }

    /**
     * Build a handler stack.
     */
    protected function getHandlerStack()
    {
        if ($this->handlerStack) {
            return $this->handlerStack;
        }

        $this->handlerStack = HandlerStack::create($this->getGuzzleHandler());

        foreach ($this->middlewares as $name => $middleware) {
            $this->handlerStack->push($middleware, $name);
        }

        return $this->handlerStack;
    }

    protected function fixJsonIssue(array $options)
    {
        if (isset($options['json']) && is_array($options['json'])) {
            $options['headers'] = array_merge(isset($options['headers']) ? $options['headers'] : [], ['Content-Type' => 'application/json']);

            if (empty($options['json'])) {
                $options['body'] = \GuzzleHttp\json_encode($options['json'], JSON_FORCE_OBJECT);
            } else {
                $options['body'] = \GuzzleHttp\json_encode($options['json'], JSON_UNESCAPED_UNICODE);
            }

            unset($options['json']);
        }

        return $options;
    }

    /**
     * Get guzzle handler.
     *
     * @return callable
     */
    protected function getGuzzleHandler()
    {
        if (property_exists($this, 'app') && isset($this->app['guzzle_handler'])) {
            return is_string($handler = $this->app->raw('guzzle_handler'))
                ? new $handler()
                : $handler;
        }

        return \GuzzleHttp\choose_handler();
    }

    /**
     * @param array $credentials
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 10:06
     */
    protected function requestToken($credentials)
    {
        $response = $this->sendRequest($credentials);
        $contents = $response->getBody()->getContents();

        $result = json_decode($contents, true);

        if (!$result && is_string($contents)) $result = AcpService::parseQString($contents);

        if (empty($result) || (isset($result['resp']) && $result['resp'] != '00') || (!isset($result['resp']) && !isset($result['respCode'])))
        {
            throw new \Exception('Request fail: '.json_encode($result, JSON_UNESCAPED_UNICODE));
        }

        return isset($result['resp']) && isset($result['params']) ? $result['params'] : $result;
    }

    /**
     * Send http request.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendRequest($credentials)
    {
        $options = [
            ('GET' === $this->requestMethod) ? 'query' : $this->config['http_post_data_type'] => $credentials,
        ];
        return $this->setHttpClient($this->app['http_client'])->request($this->getEndpoint(), $this->requestMethod, $options);
    }

    /**
     * @return string
     * @author cfn <cfn@leapy.cn>
     * @date 2021/8/16 10:04
     */
    protected function getEndpoint()
    {
        // payment_model、test_file_uri、test_base_uri是支付中的配置参数
        if (isset($this->config['payment_model']))
        {
            // 文件下载时endpoint 为空
            $this->endpoint = empty($this->endpoint) ?
                ($this->config['payment_model'] ? $this->config['test_file_uri'] : $this->config['file_uri'])
                :
                ((($this->config['payment_model']) ? $this->config['test_base_uri'] : $this->config['base_uri']) . $this->endpoint);
        }

        return $this->endpoint;
    }
}
