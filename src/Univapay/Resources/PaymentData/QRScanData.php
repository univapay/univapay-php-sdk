<?php

namespace Univapay\Resources\PaymentData;

use JsonSerializable;
use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class QRScanData
{
    use Jsonable;

    public $gateway;

    public function __construct($gateway)
    {
        $this->gateway = $gateway;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
