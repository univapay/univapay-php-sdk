<?php

namespace Univapay\Resources\PaymentMethod;

use JsonSerializable;
use Univapay\Enums\ConvenienceStore;
use Univapay\Enums\Field;
use Univapay\Enums\PaymentType;
use Univapay\Enums\Reason;
use Univapay\Enums\TokenType;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Resources\PaymentData\ConvenienceStoreData;
use Univapay\Resources\PaymentData\PhoneNumber;
use Univapay\Utility\FunctionalUtils;

class ConvenienceStorePayment extends PaymentMethod implements JsonSerializable
{
    private $convenienceStoreData;

    public function __construct(
        $email,
        ConvenienceStoreData $convenienceStoreData,
        TokenType $type = null,
        UsageLimit $usageLimit = null,
        array $metadata = null
    ) {
        if ($convenienceStoreData->phoneNumber->countryCode != PhoneNumber::JP) {
            throw new UnivapayValidationError(
                Field::PHONE_NUMBER(),
                Reason::ONLY_JAPANESE_PHONE_NUMBER_ALLOWED()
            );
        }
        if (isset($convenienceStoreData->expirationPeriod) &&
        ($convenienceStoreData->expirationPeriod->d < 7 || $convenienceStoreData->expirationPeriod->d > 30)) {
            throw new UnivapayValidationError(
                Field::EXPIRATION_PERIOD(),
                Reason::EXPIRATION_DATE_OUT_OF_BOUNDS()
            );
        }

        parent::__construct(PaymentType::KONBINI(), $type, $email, $usageLimit, $metadata);
        $this->convenienceStoreData = $convenienceStoreData;
    }

    // Accepts all types
    protected function acceptsTokenType(TokenType $tokenType = null)
    {
    }

    public function jsonSerialize()
    {
        $parentData = parent::jsonSerialize();
        $parentData['data'] = $this->convenienceStoreData->jsonSerialize();

        return $parentData;
    }
}
