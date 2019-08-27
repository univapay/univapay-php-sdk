<?php

namespace Univapay\Errors;

use Throwable;

class UnivapayNoMoreItemsError extends UnivapayError
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('No more items in list', $code, $previous);
    }
}
