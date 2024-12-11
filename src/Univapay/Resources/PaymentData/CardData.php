<?php

namespace Univapay\Resources\PaymentData;

use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class CardData
{
    use Jsonable;

    public $card;
    public $billing;
    public $cvvAuthorize;
    public $threeDS;

    public function __construct($card, $billing, $cvvAuthorize, $threeDS)
    {
        $this->card = $card;
        $this->billing = $billing;
        $this->cvvAuthorize = $cvvAuthorize;
        $this->threeDS = $threeDS;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('card', true, Card::getSchema()->getParser())
            ->upsert('billing', false, BillingData::getSchema()->getParser())
            ->upsert('cvv_authorize', true, CvvAuthorize::getSchema()->getParser())
            ->upsert('three_ds', false, TokenThreeDS::getSchema()->getParser());
    }
}
