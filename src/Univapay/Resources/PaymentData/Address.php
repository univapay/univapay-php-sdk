<?php
namespace Univapay\Resources\PaymentData;

use JsonSerializable;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\Json\JsonSchema;

class Address implements JsonSerializable
{
    use Jsonable;

    public $line1;
    public $line2;
    public $state;
    public $city;
    public $country;
    public $zip;

    public function __construct(
        $line1 = null,
        $line2 = null,
        $state = null,
        $city = null,
        $country = null,
        $zip = null
    ) {
        $this->line1 = $line1;
        $this->line2 = $line2;
        $this->state = $state;
        $this->city = $city;
        $this->country = $country;
        $this->zip = $zip;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }

    public function jsonSerialize()
    {
        $data = [
            'line1' => $this->line1,
            'line2' => $this->line2,
            'state' => $this->state,
            'city' => $this->city,
            'country' => $this->country,
            'zip' => $this->zip
        ];
        return FunctionalUtils::stripNulls($data);
    }
}
