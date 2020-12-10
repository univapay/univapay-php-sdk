<?php

namespace Univapay\Resources\PaymentMethod;

use JsonSerializable;
use Univapay\Enums\Field;
use Univapay\Enums\Gateway;
use Univapay\Enums\PaymentType;
use Univapay\Enums\QrBrandMerchant;
use Univapay\Enums\Reason;
use Univapay\Enums\TokenType;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Utility\FunctionalUtils;

class QrMerchantPayment extends PaymentMethod implements JsonSerializable
{
    private $brand;

    public function __construct(
        $email,
        QrBrandMerchant $brand,
        array $metadata = null,
        $ipAddress = null
    ) {
        parent::__construct(PaymentType::QR_MERCHANT(), null, $email, $ipAddress, null, $metadata);
        $this->brand = $brand;
    }

    // Does not take in a token type
    protected function acceptsTokenType(TokenType $tokenType = null)
    {
    }

    public function jsonSerialize()
    {
        $parentData = parent::jsonSerialize();
        $parentData['data'] = ['brand' => $this->brand->getName()];

        return $parentData;
    }
}
