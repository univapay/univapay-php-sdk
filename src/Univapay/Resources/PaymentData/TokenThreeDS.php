<?php

namespace Univapay\Resources\PaymentData;

use Univapay\Enums\ThreeDSStatus;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\Json\JsonSchema;

class TokenThreeDS
{
    use Jsonable;

    public $enabled;
    public $redirectEndpoint;
    public $status;
    public $redirectId;
    public $error;

    /**
     * Three DS for Transaction Token
     *
     * @param bool $enabled enable 3DS for this transaction
     * @param int $redirectEndpoint redirect endpoint, where the user will be redirected after 3DS authentication
     */
    public function __construct(
        $enabled,
        $redirectEndpoint,
        ThreeDSStatus $status = null,
        $redirectId = null,
        $error = null
    ) {
        $this->enabled = $enabled;
        $this->redirectEndpoint = $redirectEndpoint;
        $this->status = $status;
        $this->redirectId = $redirectId;
        $this->error = $error;
    }

    public function jsonSerialize() : array
    {
        return FunctionalUtils::stripNulls([
            'enabled' => $this->enabled,
            'redirect_endpoint' => $this->redirectEndpoint
        ]);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('status', false, FormatterUtils::getTypedEnum(ThreeDSStatus::class));
    }
}
