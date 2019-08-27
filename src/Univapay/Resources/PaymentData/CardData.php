<?php

namespace Univapay\Resources\PaymentData;

use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class CardData
{
    use Jsonable;

    public $card;
    public $billing;

    public function __construct($card, $billing)
    {
        $this->card = $card;
        $this->billing = $billing;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('card', true, Card::getSchema()->getParser())
            ->upsert('billing', false, BillingData::getSchema()->getParser());
    }
}
