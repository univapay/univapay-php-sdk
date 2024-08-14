<?php

namespace Univapay\Resources\PaymentMethod;

use JsonSerializable;
use Univapay\Enums\PaymentType;
use Univapay\Enums\TokenType;
use Univapay\Enums\UsageLimit;
use Univapay\Utility\FunctionalUtils;

abstract class PaymentMethod implements JsonSerializable
{
    private $email;
    private $ipAddress;
    public $paymentType;
    public $type;
    public $usageLimit;
    public $metadata;

    protected function __construct(
        PaymentType $paymentType,
        TokenType $type = null,
        $email = null,
        $ipAddress = null,
        UsageLimit $usageLimit = null,
        array $metadata = null
    ) {
        $this->acceptsTokenType($type);
        
        $this->email = $email;
        $this->ipAddress = $ipAddress;
        $this->paymentType = $paymentType;
        $this->type = $type;
        $this->usageLimit = $usageLimit;
        $this->metadata = $metadata;
    }

    // Returns void if this payment method accepts the token type
    // Throws UnivapayValidationError if not valid
    abstract protected function acceptsTokenType(TokenType $type = null);

    public function jsonSerialize() : array
    {
        return FunctionalUtils::stripNulls([
            'email' => $this->email,
            'ip_address' => $this->ipAddress,
            'payment_type' => $this->paymentType->getValue(),
            'type' => isset($this->type) ? $this->type->getValue() : null,
            'usage_limit' => isset($this->usageLimit) ? $this->usageLimit->getValue() : null,
            'metadata' => $this->metadata
        ]);
    }
}
