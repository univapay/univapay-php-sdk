<?php

namespace Univapay\Errors;

use Throwable;

class UnivapayUnknownWebhookEvent extends UnivapayError
{
    public function __construct($event, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Received unknown event [$event]", $code, $previous);
    }
}
