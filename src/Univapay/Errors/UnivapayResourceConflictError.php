<?php

namespace Univapay\Errors;

use Throwable;

class UnivapayResourceConflictError extends UnivapayRequestError
{
    public function __construct($url = "", $json = [])
    {
        parent::__construct(
            $url,
            $json['status'],
            $json['code'],
            $json['errors'],
            409
        );
    }
}
