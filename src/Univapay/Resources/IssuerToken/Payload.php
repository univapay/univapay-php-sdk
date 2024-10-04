<?php

namespace Univapay\Resources\IssuerToken;

use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class Payload
{
    use Jsonable;
    public $resourceId;
    public $resourceType;

    public function __construct(
        $resourceId = null,
        $resourceType = null
    ) {
        $this->resourceId = $resourceId;
        $this->resourceType = $resourceType;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
