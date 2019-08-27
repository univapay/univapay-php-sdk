<?php

namespace Univapay\Resources\PaymentMethod;

use JsonSerializable;
use Univapay\Enums\PaymentType;
use Univapay\Enums\TokenType;
use Univapay\Resources\PaymentData\Address;
use Univapay\Utility\FunctionalUtils;

class ApplePayPayment extends PaymentMethod implements JsonSerializable
{
    private $cardholder;
    private $applePayToken;
    private $address;
    private $phoneNumber;

    public function __construct(
        $email,
        $cardholder,
        $applePayToken,
        TokenType $type = null,
        UsageLimit $usageLimit = null,
        Address $address = null,
        PhoneNumber $phoneNumber = null,
        array $metadata = null
    ) {
        parent::__construct(PaymentType::APPLE_PAY(), $type, $email, $usageLimit, $metadata);
        $this->cardholder = $cardholder;
        $this->applePayToken = $applePayToken;
        $this->address = $address;
        $this->phoneNumber = $phoneNumber;
    }

    // Accepts all types
    protected function acceptsTokenType(TokenType $tokenType = null)
    {
    }

    public function jsonSerialize()
    {
        $parentData = parent::jsonSerialize();
        $parentData['data'] = [
            'applepay_token' => $this->applePayToken,
            'cardholder' => $this->cardholder
        ] + (isset($this->address)
            ? $this->address->jsonSerialize()
            : [])
        + (isset($this->phoneNumber)
            ? $this->phoneNumber->jsonSerialize()
            : []);

        return FunctionalUtils::stripNulls($parentData);
    }
}
