<?php

namespace Univapay\Resources\PaymentMethod;

use JsonSerializable;
use Univapay\Enums\PaymentType;
use Univapay\Enums\QrBrandMerchant;
use Univapay\Enums\TokenType;

class QrMerchantPayment extends PaymentMethod implements JsonSerializable
{
    private $brand;

    public function __construct(
        $email,
        QrBrandMerchant $brand,
        ?array $metadata = null,
        $ipAddress = null
    ) {
        parent::__construct(PaymentType::QR_MERCHANT(), null, $email, $ipAddress, null, $metadata);
        $this->brand = $brand;
    }

    // Does not take in a token type
    protected function acceptsTokenType(?TokenType $tokenType = null)
    {
    }

    public function jsonSerialize(): array
    {
        $parentData = parent::jsonSerialize();
        $parentData['data'] = ['brand' => $this->brand->getName()];

        return $parentData;
    }
}
