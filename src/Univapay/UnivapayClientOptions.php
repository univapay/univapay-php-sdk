<?php

namespace Univapay;

use Univapay\Requests\Handlers\NetworkRetryHandler;
use Univapay\Requests\Handlers\RateLimitHandler;
use Univapay\Utility\FunctionalUtils;

class UnivapayClientOptions
{
    /**
     * [String] Sets the endpoint the SDK connects to
     */
    public $endpoint;

    /**
     * [RateLimitHandler] The instance of the rate limit handler to use
     */
    public $rateLimitHandler;

    /**
     * [NetworkRetryHandler] The instance of the network retry handler to use
     */
    public $networkRetryHandler;

    /**
     * [Headers] Customs headers to be sent with each request
     * @var array
     */
    public $headers;

    public function __construct(
        $endpoint = 'https://api.univapay.com',
        $headers = []
    ) {
        $this->endpoint = $endpoint;
        $this->rateLimitHandler = new RateLimitHandler();
        $this->networkRetryHandler = new NetworkRetryHandler();
        $this->headers = $headers;
    }

    public function getRequestHandlers()
    {
        return FunctionalUtils::stripNulls([
            $this->rateLimitHandler,
            $this->networkRetryHandler
        ]);
    }
}
