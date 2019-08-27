<?php

namespace Univapay\Resources\Configuration;

use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class InstallmentsConfiguration
{
    use Jsonable;

    public $enabled;
    public $minChargeAmount;
    public $maxPayoutPeriod;
    public $failedCyclesToCancel;

    public function __construct($enabled, $minChargeAmount, $maxPayoutPeriod, $failedCyclesToCancel)
    {
        $this->enabled = $enabled;
        $this->minChargeAmount = $minChargeAmount;
        $this->maxPayoutPeriod = $maxPayoutPeriod;
        $this->failedCyclesToCancel = $failedCyclesToCancel;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
