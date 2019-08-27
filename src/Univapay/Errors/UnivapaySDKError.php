<?php

namespace Univapay\Errors;

use Univapay\Enums\Reason;

class UnivapaySDKError extends UnivapayError
{
    public function __construct(Reason $reason)
    {
        parent::__construct($reason->getValue());
    }
}
