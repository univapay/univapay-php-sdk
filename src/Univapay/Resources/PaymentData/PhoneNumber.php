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

    const AU = 61;
    const BR = 55;
    const CA = 1;
    const CH = 41;
    const CN = 86;
    const DE = 49;
    const FR = 33;
    const GB = 44;
    const IT = 39;
    const JP = 81;
    const KR = 82;
    const MT = 356;
    const PH = 63;
    const PL = 48;
    const RU = 7;
    const SE = 46;
    const SG = 65;
    const SV = 503;
    const TH = 66;
    const TW = 886;
    const US = 1;
    const ZA = 27;

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

    public function jsonSerialize()
    {
        $data = [
            'country_code' => $this->countryCode,
            'local_number' => $this->localNumber
        ];
        return FunctionalUtils::stripNulls($data);
    }
}
