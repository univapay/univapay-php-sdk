<?php

namespace Univapay\Resources\IssuerToken;

use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class ThreeDS
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
            ->upsert('payload', false, Payload::getSchema()->getParser());
    }
}
