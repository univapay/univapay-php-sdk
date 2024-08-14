<?php

namespace Univapay\Resources\PaymentMethod;

use JsonSerializable;
use Univapay\Enums\Field;
use Univapay\Enums\PaymentType;
use Univapay\Enums\Reason;
use Univapay\Enums\TokenType;
use Univapay\Enums\UsageLimit;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Resources\PaymentData\PaidyData;
use Univapay\Resources\PaymentData\PhoneNumber;
use Univapay\Utility\FunctionalUtils;

class PaidyPayment extends PaymentMethod implements JsonSerializable
{
    private $paidyData;

    public function __construct(
        PaidyData $paidyData,
        $email = null,
        TokenType $type = null,
        UsageLimit $usageLimit = null,
        array $metadata = null,
        $ipAddress = null
    ) {
        if (isset($paidyData->phoneNumber) && $paidyData->phoneNumber->countryCode != PhoneNumber::JP) {
            throw new UnivapayValidationError(
                Field::PHONE_NUMBER(),
                Reason::ONLY_JAPANESE_PHONE_NUMBER_ALLOWED()
            );
        }

        if (isset($paidyData->shippingAddress) && is_null($paidyData->shippingAddress->zip)) {
            throw new UnivapayValidationError(
                Field::ZIP(),
                Reason::REQUIRED_VALUE()
            );
        }

        parent::__construct(PaymentType::PAIDY(), $type, $email, $ipAddress, $usageLimit, $metadata);
        $this->paidyData = $paidyData;
    }

    // Accepts all types
    protected function acceptsTokenType(TokenType $tokenType = null)
    {
    }

    public function jsonSerialize() : array
    {
        $parentData = parent::jsonSerialize();
        $parentData['data'] = $this->paidyData->jsonSerialize();

        return $parentData;
    }
}
