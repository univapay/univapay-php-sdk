<?php

namespace Univapay\Resources;

use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class Payload
{
    use Jsonable;
    public $resourceId;
    public $resourceType;

    public function __construct(
        $resourceId,
        $resourceType
    ) {
        $this->resourceId = $resourceId;
        $this->resourceType = $resourceType;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
