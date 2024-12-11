<?php

namespace Univapay\Resources;

use Univapay\Enums\ThreeDSMode;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class PaymentThreeDS
{
    use Jsonable;

    public $redirectEndpoint;
    public $redirectId;
    public $mode;

    public function __construct(
        $redirectEndpoint,
        $redirectId,
        $mode
    ) {
        $this->redirectEndpoint = $redirectEndpoint;
        $this->redirectId = $redirectId;
        $this->mode = $mode;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('mode', false, FormatterUtils::getTypedEnum(ThreeDSMode::class));
    }

    public function jsonSerialize()
    {
        return [
            'redirect_endpoint' => $this->redirectEndpoint,
            'mode' => $this->mode
        ];
    }
}
