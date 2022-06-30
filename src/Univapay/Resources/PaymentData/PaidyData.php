<?php

namespace Univapay\Resources\PaymentData;

use JsonSerializable;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\Json\JsonSchema;

class PaidyData implements JsonSerializable
{
    use Jsonable;

    public $paidyToken;
    public $shippingAddress;
    public $phoneNumber;

    public function __construct(
        $paidyToken,
        Address $shippingAddress,
        PhoneNumber $phoneNumber = null
    ) {
        $this->paidyToken = $paidyToken;
        $this->shippingAddress = $shippingAddress;
        $this->phoneNumber = $phoneNumber;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('phone_number', false, PhoneNumber::getSchema()->getParser())
            ->upsert('shipping_address', true, Address::getSchema()->getParser());
    }

    public function jsonSerialize()
    {
        return FunctionalUtils::stripNulls([
            'paidy_token' => $this->paidyToken,
            'shipping_address' => $this->shippingAddress->jsonSerialize(),
            'phone_number' => isset($this->phoneNumber) ? $this->phoneNumber->jsonSerialize() : null
        ]);
    }
}
