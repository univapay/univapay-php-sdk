<?php

namespace Univapay\Resources\PaymentData;

use JsonSerializable;
use Univapay\Enums\QrBrandMerchant;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class QrMerchantData
{
    use Jsonable;

    public $brand;

    public function __construct(QrBrandMerchant $brand)
    {
        $this->brand = $brand;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('brand', true, FormatterUtils::getTypedEnum(QrBrandMerchant::class));
    }
}
