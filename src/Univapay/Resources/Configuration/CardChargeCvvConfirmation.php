<?php

namespace Univapay\Resources\Configuration;

use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class CardChargeCvvConfirmation
{
    use Jsonable;

    public $enabled;
    public $threshold;

    public function __construct($enabled, $threshold)
    {
        $this->enabled = $enabled;
        $this->threshold = $threshold;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
