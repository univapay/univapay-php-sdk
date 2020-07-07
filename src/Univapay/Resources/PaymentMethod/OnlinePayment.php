<?php

namespace Univapay\Resources\PaymentMethod;

use JsonSerializable;
use Univapay\Enums\Field;
use Univapay\Enums\Gateway;
use Univapay\Enums\PaymentType;
use Univapay\Enums\Reason;
use Univapay\Enums\TokenType;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Utility\FunctionalUtils;

class OnlinePayment extends PaymentMethod implements JsonSerializable
{
    private $gateway;

    public function __construct(
        $email,
        Gateway $gateway,
        array $metadata = null
    ) {
        parent::__construct(PaymentType::ONLINE(), null, $email, null, $metadata);
        $this->gateway = $gateway;
    }

    // Does not take in a token type
    protected function acceptsTokenType(TokenType $tokenType = null)
    {
    }

    public function jsonSerialize()
    {
        $parentData = parent::jsonSerialize();
        $parentData['data'] = ['gateway' => $this->gateway->getName()];

        return $parentData;
    }
}
