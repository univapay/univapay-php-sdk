<?php

namespace Univapay\Resources\Authentication;

use Exception;
use Throwable;

class InvalidJWTFormat extends Exception
{
    public function __construct($msg, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Unparsable JWT: $msg", $code, $previous);
    }
}
