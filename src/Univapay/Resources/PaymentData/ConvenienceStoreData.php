<?php

namespace Univapay\Resources\PaymentData;

use DateInterval;
use JsonSerializable;
use Univapay\Enums\ConvenienceStore;
use Univapay\Enums\Field;
use Univapay\Enums\Reason;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\Json\JsonSchema;

class ConvenienceStoreData implements JsonSerializable
{
    use Jsonable;

    public $customerName;
    public $phoneNumber;
    public $convenienceStore;
    public $expirationPeriod;

    public function __construct(
        $customerName,
        PhoneNumber $phoneNumber,
        ConvenienceStore $convenienceStore,
        DateInterval $expirationPeriod = null
    ) {
        $this->customerName = $customerName;
        $this->phoneNumber = $phoneNumber;
        $this->convenienceStore = $convenienceStore;
        $this->expirationPeriod = $expirationPeriod;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('phone_number', true, PhoneNumber::getSchema()->getParser())
            ->upsert('convenience_store', true, FormatterUtils::getTypedEnum(ConvenienceStore::class))
            ->upsert('expiration_period', true, FormatterUtils::of('getDateInterval'));
    }

    public function jsonSerialize() : array
    {
        return FunctionalUtils::stripNulls([
            'customer_name' => $this->customerName,
            'convenience_store' => $this->convenienceStore->getValue(),
            'expiration_period' => isset($this->expirationPeriod)
                ? FormatterUtils::formatDateIntervalISO($this->expirationPeriod)
                : null,
            'phone_number' => $this->phoneNumber->jsonSerialize()
        ]);
    }
}
