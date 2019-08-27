<?php

namespace Univapay\Resources\Configuration;

use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class SecurityConfiguration
{

    use Jsonable;

    public $inspectSuspiciousLoginAfter;
    public $refundPercentLimit;
    public $limitChargeByCardConfiguration;
    public $confirmationRequired;

    public function __construct(
        $inspectSuspiciousLoginAfter,
        $refundPercentLimit,
        $limitChargeByCardConfiguration,
        $confirmationRequired
    ) {
        $this->inspectSuspiciousLoginAfter = $inspectSuspiciousLoginAfter;
        $this->refundPercentLimit = $refundPercentLimit;
        $this->limitChargeByCardConfiguration = $limitChargeByCardConfiguration;
        $this->confirmationRequired = $confirmationRequired;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
                ->upsert(
                    'limit_charge_by_card_configuration',
                    false,
                    LimitChargeByCardConfiguration::getSchema()->getParser()
                );
    }
}
