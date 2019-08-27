<?php

namespace Univapay\Resources\Configuration;

use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class RecurringConfiguration
{
    use Jsonable;

    public $recurringType;
    public $chargeWaitPeriod;
    public $cardChargeCvvConfirmation;

    public function __construct($recurringType, $chargeWaitPeriod, $cardChargeCvvConfirmation)
    {
        $this->recurringType = $recurringType;
        $this->chargeWaitPeriod = $chargeWaitPeriod;
        $this->cardChargeCvvConfirmation = $cardChargeCvvConfirmation;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert(
                'card_charge_cvv_confirmation',
                true,
                $formatter = CardChargeCvvConfirmation::getSchema()->getParser()
            );
    }
}
