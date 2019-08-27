<?php

namespace Univapay\Errors;

use Throwable;

class UnivapayUnauthorizedError extends UnivapayRequestError
{
    public function __construct($url = "", $json = [])
    {
        parent::__construct(
            $url,
            $json['status'],
            $json['code'],
            $json['errors'],
            401
        );
    }
}
