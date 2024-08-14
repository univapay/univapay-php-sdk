<?php

namespace Univapay\Resources\PaymentMethod;

use JsonSerializable;
use Univapay\Enums\Field;
use Univapay\Enums\PaymentType;
use Univapay\Enums\Reason;
use Univapay\Enums\TokenType;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Resources\PaymentData\Address;
use Univapay\Resources\PaymentData\CvvAuthorize;
use Univapay\Resources\PaymentData\PhoneNumber;
use Univapay\Utility\FunctionalUtils;

class CardPayment extends PaymentMethod implements JsonSerializable
{
    private $cardholder;
    private $cardNumber;
    private $expMonth;
    private $expYear;
    private $cvv;
    private $address;
    private $phoneNumber;
    private $cvvAuthorize;

    public function __construct(
        $email,
        $cardholder,
        $cardNumber,
        $expMonth,
        $expYear,
        $cvv,
        TokenType $type = null,
        UsageLimit $usageLimit = null,
        Address $address = null,
        PhoneNumber $phoneNumber = null,
        array $metadata = null,
        CvvAuthorize $cvvAuthorize = null,
        $ipAddress = null
    ) {
        parent::__construct(PaymentType::CARD(), $type, $email, $ipAddress, $usageLimit, $metadata);
        $this->cardholder = $cardholder;
        $this->cardNumber = $cardNumber;
        $this->expMonth = $expMonth;
        $this->expYear = $expYear;
        $this->cvv = $cvv;
        $this->address = $address;
        $this->phoneNumber = $phoneNumber;
        $this->cvvAuthorize = $cvvAuthorize;

        // Extra validation required due to late init of cvvAuthorize
        $this->acceptsTokenType($type);
    }

    // Accepts all types
    protected function acceptsTokenType(TokenType $tokenType = null)
    {
        if (isset($this->cvvAuthorize) && TokenType::RECURRING() !== $tokenType) {
            throw new UnivapayValidationError(Field::TYPE(), Reason::TRANSACTION_TOKEN_IS_NOT_RECURRING());
        }
    }

    public function jsonSerialize() : array
    {
        $parentData = parent::jsonSerialize();
        $parentData['data'] = [
            'cardholder' => $this->cardholder,
            'card_number' => $this->cardNumber,
            'exp_month' => $this->expMonth,
            'exp_year' => $this->expYear,
            'cvv' => $this->cvv,
            'phone_number' => isset($this->phoneNumber)
                ? $this->phoneNumber->jsonSerialize()
                : null,
            'cvv_authorize' => isset($this->cvvAuthorize)
                ? $this->cvvAuthorize->jsonSerialize()
                : null
        ] + (isset($this->address)
            ? $this->address->jsonSerialize()
            : []
        );
        return FunctionalUtils::stripNulls($parentData);
    }
}
