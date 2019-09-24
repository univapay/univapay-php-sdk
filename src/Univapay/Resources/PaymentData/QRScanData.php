<?php

namespace Univapay\Resources\PaymentData;

use JsonSerializable;
use Univapay\Enums\Gateway;
use Univapay\Enums\QRBrand;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class QRScanData
{
    use Jsonable;

    public $gateway;
    public $brand;

    public function __construct(Gateway $gateway, QRBrand $brand)
    {
        $this->gateway = $gateway;
        $this->brand = $brand;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('gateway', true, FormatterUtils::getTypedEnum(Gateway::class))
            ->upsert('brand', true, FormatterUtils::getTypedEnum(QRBrand::class));
    }
}
