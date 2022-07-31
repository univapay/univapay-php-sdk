<?php

namespace Univapay\Requests\Handlers;

use Closure;
use WpOrg\Requests\Exception;

class NetworkRetryHandler extends BasicRetryHandler
{
    public function __construct($tries = 3, $interval = 1)
    {
        parent::__construct(Exception::class, $tries, $interval);
    }
}
