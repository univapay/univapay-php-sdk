<?php

namespace Univapay\Requests\Handlers;

use Closure;
use Univapay\Errors\UnivapayRateLimitedError;

class RateLimitHandler extends BasicRetryHandler
{
    public function __construct($tries = 3, $interval = 1)
    {
        parent::__construct(UnivapayRateLimitedError::class, $tries, $interval);
    }
}
