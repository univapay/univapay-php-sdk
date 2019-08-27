<?php

namespace Univapay\Errors;

use Throwable;

class UnivapayServerError extends UnivapayError
{
    public function __construct($httpCode, $url, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Unexpected server error ($httpCode) reached while requesting $url", $code, $previous);
    }
}
