<?php

namespace Univapay\Errors;

use Throwable;

class UnivapayNotFoundError extends UnivapayError
{
    public function __construct($url, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Route $url not found", $code, $previous);
    }
}
