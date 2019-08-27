<?php

namespace Univapay\Resources\PaymentMethod;

use JsonSerializable;
use Univapay\Enums\Field;
use Univapay\Enums\PaymentType;
use Univapay\Enums\Reason;
use Univapay\Enums\TokenType;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Utility\FunctionalUtils;

class QRScanPayment extends PaymentMethod implements JsonSerializable
{
    private $scannedQr;

    public function __construct(
        $email,
        $scannedQr,
        array $metadata = null
    ) {
        parent::__construct(PaymentType::QR_SCAN(), null, $email, null, $metadata);
        $this->scannedQr = $scannedQr;
    }

    // Does not take in a token type
    protected function acceptsTokenType(TokenType $tokenType = null)
    {
    }

    public function jsonSerialize()
    {
        $parentData = parent::jsonSerialize();
        $parentData['data'] = ['scanned_qr' => $this->scannedQr];

        return $parentData;
    }
}
