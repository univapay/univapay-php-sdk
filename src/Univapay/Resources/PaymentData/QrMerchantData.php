<?php

namespace Univapay\Resources\PaymentData;

use JsonSerializable;
use Univapay\Enums\Gateway;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class QrMerchantData
{
    use Jsonable;

    public $gateway;

    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('gateway', true, FormatterUtils::getTypedEnum(Gateway::class));
    }
}
