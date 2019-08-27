<?php

namespace Univapay\Errors;

use Univapay\Enums\Reason;

class UnivapayLogicError extends UnivapayRequestError
{
    public function __construct(Reason $reason)
    {
        parent::__construct('preflight', 'error', $reason->getValue(), null);
    }
}
