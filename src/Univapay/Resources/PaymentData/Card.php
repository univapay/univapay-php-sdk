<?php

namespace Univapay\Resources\PaymentData;

use Univapay\Enums\CardBrand;
use Univapay\Enums\CardCategory;
use Univapay\Enums\CardSubBrand;
use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class Card
{
    use Jsonable;

    public $cardholder;
    public $expMonth;
    public $expYear;
    public $lastFour;
    public $brand;
    public $country;
    public $category;
    public $issuer;
    public $subBrand;

    public function __construct(
        $cardholder,
        $expMonth,
        $expYear,
        $lastFour,
        $brand,
        $country,
        $category,
        $issuer,
        $subBrand
    ) {
        $this->cardholder = $cardholder;
        $this->expMonth = $expMonth;
        $this->expYear = $expYear;
        $this->lastFour = $lastFour;
        $this->brand = CardBrand::fromValue($brand);
        $this->country = $country;
        $this->category = CardCategory::fromValue($category);
        $this->issuer = $issuer;
        $this->subBrand = CardSubBrand::fromValue($subBrand);
    }

    public static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
