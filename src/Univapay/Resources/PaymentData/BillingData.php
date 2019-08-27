<?php
namespace Univapay\Resources\PaymentData;

use JsonSerializable;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\Json\JsonSchema;

class BillingData implements JsonSerializable
{
    use Jsonable;

    public $line1;
    public $line2;
    public $state;
    public $city;
    public $country;
    public $zip;
    public $phoneNumber;

    public function __construct(
        $line1 = null,
        $line2 = null,
        $state = null,
        $city = null,
        $country = null,
        $zip = null,
        PhoneNumber $phoneNumber = null
    ) {
        $this->line1 = $line1;
        $this->line2 = $line2;
        $this->state = $state;
        $this->city = $city;
        $this->country = $country;
        $this->zip = $zip;
        $this->phoneNumber = $phoneNumber;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('phone_number', false, PhoneNumber::getSchema()->getParser());
    }

    public function jsonSerialize()
    {
        $data = [
            'line1' => $this->line1,
            'line2' => $this->line2,
            'state' => $this->state,
            'city' => $this->city,
            'country' => $this->country,
            'zip' => $this->zip,
            'phone_number' => isset($this->phoneNumber) ? $this->phoneNumber->jsonSerialize() : null
        ];
        return FunctionalUtils::stripNulls($data);
    }
}
