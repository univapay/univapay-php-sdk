<?php

namespace Univapay\Resources\PaymentData;

use JsonSerializable;
use Univapay\Enums\CallMethod;
use Univapay\Enums\OnlineBrand;
use Univapay\Enums\OsType;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class OnlineData
{
    use Jsonable;

    public $brand;
    public $callMethod;
    public $userIdentifier;
    public $osType;
    public $issuerToken;

    public function __construct(
        OnlineBrand $brand,
        CallMethod $callMethod = null,
        $userIdentifier = null,
        OsType $osType = null,
        $issuerToken = null
    ) {
        $this->brand = $brand;
        $this->callMethod = $callMethod;
        $this->userIdentifier = $userIdentifier;
        $this->osType = $osType;
        $this->issuerToken = $issuerToken;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('brand', true, FormatterUtils::getTypedEnum(OnlineBrand::class))
            ->upsert('call_method', true, FormatterUtils::getTypedEnum(CallMethod::class))
            ->upsert('os_type', false, FormatterUtils::getTypedEnum(OsType::class));
    }
}
