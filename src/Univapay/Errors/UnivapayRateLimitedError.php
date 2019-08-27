<?php

namespace Univapay\Errors;

use Throwable;

class UnivapayRateLimitedError extends UnivapayError
{
    public function __construct($url, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Rate limited on $url", $code, $previous);
    }
}
