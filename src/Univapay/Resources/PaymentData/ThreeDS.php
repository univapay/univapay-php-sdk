<?php

namespace Univapay\Resources\PaymentData;

use Univapay\Enums\ThreeDSStatus;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\Json\JsonSchema;

class ThreeDS
{
    use Jsonable;

    public $enabled;
    public $status;
    public $redirectEndpoint;
    public $redirectId;

    public function __construct(
        $enabled = null,
        $status = null,
        $redirectEndpoint = null,
        $redirectId = null
    ) {
        $this->enabled = $enabled;
        $this->status = $status;
        $this->redirectEndpoint = $redirectEndpoint;
        $this->redirectId = $redirectId;
    }

    public function jsonSerialize() : array
    {
        return FunctionalUtils::stripNulls([
            'enabled' => $this->enabled,
            'status' => $this->status,
            'redirect_endpoint' => $this->redirectEndpoint,
            'redirect_id' => $this->redirectId
        ]);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('status', false, FormatterUtils::getTypedEnum(ThreeDSStatus::class));
    }
}
