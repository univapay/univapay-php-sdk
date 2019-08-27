<?php

namespace Univapay;

use Univapay\Requests\Handlers\RateLimitHandler;

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

    public function __construct(
        $endpoint = 'https://api.univapay.com'
    ) {
        $this->endpoint = $endpoint;
        $this->rateLimitHandler = new RateLimitHandler();
    }
}
