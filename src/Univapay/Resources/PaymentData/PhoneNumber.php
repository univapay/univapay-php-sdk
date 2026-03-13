<?php

namespace Univapay\Resources\PaymentData;

use JsonSerializable;
use Univapay\Enums\Field;
use Univapay\Enums\Reason;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\Json\JsonSchema;

class PhoneNumber implements JsonSerializable
{
    use Jsonable;

    public const AU = 61;
    public const BR = 55;
    public const CA = 1;
    public const CH = 41;
    public const CN = 86;
    public const DE = 49;
    public const FR = 33;
    public const GB = 44;
    public const IT = 39;
    public const JP = 81;
    public const KR = 82;
    public const MT = 356;
    public const PH = 63;
    public const PL = 48;
    public const RU = 7;
    public const SE = 46;
    public const SG = 65;
    public const SV = 503;
    public const TH = 66;
    public const TW = 886;
    public const US = 1;
    public const ZA = 27;

    public $countryCode;
    public $localNumber;

    public function __construct($countryCode, $localNumber)
    {
        if (is_null($countryCode)) {
            throw new UnivapayValidationError(Field::COUNTRY_CODE(), Reason::REQUIRED_VALUE());
        }
        if (is_null($localNumber)) {
            throw new UnivapayValidationError(Field::LOCAL_NUMBER(), Reason::REQUIRED_VALUE());
        }
        if (!preg_match('/[0-9]+/', $localNumber)) {
            throw new UnivapayValidationError(Field::LOCAL_NUMBER(), Reason::INVALID_PHONE_NUMBER());
        }
        $this->countryCode = trim($countryCode, '+');
        $this->localNumber = $localNumber;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }

    public function jsonSerialize(): array
    {
        $data = [
            'country_code' => $this->countryCode,
            'local_number' => $this->localNumber
        ];
        return FunctionalUtils::stripNulls($data);
    }
}
