<?php

namespace Univapay\Resources\PaymentToken;

use Univapay\Enums\CallMethod;
use Univapay\Enums\PaymentType;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class ThreeDSIssuerToken
{
    use Jsonable;

    public $callMethod;
    public $contentType;
    public $issuerToken;
    public $payload;
    public $paymentType;

    public function __construct(
        $callMethod = null,
        $contentType = null,
        $issuerToken = null,
        $payload = null,
        $paymentType = null
    ) {
        $this->callMethod = $callMethod;
        $this->contentType = $contentType;
        $this->issuerToken = $issuerToken;
        $this->payload = $payload;
        $this->paymentType = $paymentType;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('call_method', true, FormatterUtils::getTypedEnum(CallMethod::class))
            ->upsert('payment_type', true, FormatterUtils::getTypedEnum(PaymentType::class));
    }
}
