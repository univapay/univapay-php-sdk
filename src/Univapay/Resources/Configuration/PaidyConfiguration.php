<?php

namespace Univapay\Resources\Configuration;

use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class PaidyConfiguration
{
    use Jsonable;
    public $enabled;

    public function __construct($enabled)
    {
        $this->enabled = $enabled;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
