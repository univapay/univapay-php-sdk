<?php

namespace Univapay\Resources;

use Univapay\Enums\ThreeDSMode;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class PaymentThreeDS
{
    use Jsonable;

    public $redirectEndpoint;
    public $mode;
    public $redirectId;

    /**
     * PaymentThreeDS constructor.
     *
     * @param string $redirectEndpoint
     * @param ThreeDSMode $mode Acceptable values: "FORCE", "IF_AVAILABLE", "NORMAL", "PROVIDED", "REQUIRE", "SKIP"
     * @param null $redirectId
     */
    public function __construct(
        $redirectEndpoint,
        $mode,
        $redirectId = null
    ) {
        $this->redirectEndpoint = $redirectEndpoint;
        $this->mode = $mode;
        $this->redirectId = $redirectId;
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
