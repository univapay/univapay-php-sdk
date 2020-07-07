<?php

namespace Univapay\Resources\Configuration;

use Univapay\Resources\Jsonable;
use Univapay\Utility\FunctionalUtils as fp;
use Univapay\Utility\Json\JsonSchema;

class QrConfiguration
{
    use Jsonable;
    public $enabled;
    public $forbiddenQrScanGateway;

    public function __construct($enabled, $forbiddenQrScanGateway)
    {
        $this->enabled = $enabled;
        $this->forbiddenQrScanGateway = $forbiddenQrScanGateway;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
