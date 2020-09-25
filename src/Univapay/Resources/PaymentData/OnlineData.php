<?php

namespace Univapay\Resources\PaymentData;

use JsonSerializable;
use Univapay\Enums\OnlineBrand;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class OnlineData
{
    use Jsonable;

    public $brand;

    public function __construct(OnlineBrand $brand)
    {
        $this->brand = $brand;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('brand', true, FormatterUtils::getTypedEnum(OnlineBrand::class));
    }
}
