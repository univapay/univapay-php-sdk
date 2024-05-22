<?php

namespace Univapay\Resources;

use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\Json\JsonSchema;

class Redirect
{
    use Jsonable;

    public $endpoint;
    public $redirectId;

    public function __construct(
        $endpoint,
        $redirectId = null
    ) {
        $this->endpoint = $endpoint;
        $this->redirectId = $redirectId;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }

    public function jsonSerialize()
    {
        $data = [
            'endpoint' => $this->endpoint
        ];
        return FunctionalUtils::stripNulls($data);
    }
}
