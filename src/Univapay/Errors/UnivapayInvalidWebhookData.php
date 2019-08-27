<?php

namespace Univapay\Errors;

use Throwable;

class UnivapayInvalidWebhookData extends UnivapayError
{

    public function __construct($payload, $code = 0, Throwable $previous = null)
    {
        $payloadAsString = print_r($payload, true);
        parent::__construct("$payloadAsString is not valid webhook data", $code, $previous);
    }
}
