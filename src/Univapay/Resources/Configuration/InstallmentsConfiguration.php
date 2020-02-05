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

    public function __construct($enabled, $minChargeAmount, $maxPayoutPeriod)
    {
        $this->enabled = $enabled;
        $this->minChargeAmount = $minChargeAmount;
        $this->maxPayoutPeriod = $maxPayoutPeriod;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
