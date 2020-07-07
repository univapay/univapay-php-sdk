<?php

namespace Univapay\Resources\PaymentData;

use JsonSerializable;
use Money\Currency;
use Univapay\Resources\Jsonable;
use Univapay\Enums\CvvAuthorizationStatus;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\Json\JsonSchema;

class CvvAuthorize implements JsonSerializable
{
    use Jsonable;
    
    public $enabled;
    public $currency;
    public $status;
    public $chargeId;
    public $credentialsId;

    public function __construct(
        $enabled,
        Currency $currency = null,
        CvvAuthorizationStatus $status = null,
        $chargeId = null,
        $credentialsId = null
    ) {
        $this->enabled = $enabled;
        $this->currency = $currency;
        $this->status = $status;
        $this->chargeId = $chargeId;
        $this->credentialsId = $credentialsId;
    }

    public function jsonSerialize()
    {
        return FunctionalUtils::stripNulls([
            'enabled' => $this->enabled,
            'currency' => isset($this->currency) ? $this->currency->jsonSerialize() : null
        ]);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('currency', false, FormatterUtils::of('getCurrency'))
            ->upsert('status', false, FormatterUtils::getTypedEnum(CvvAuthorizationStatus::class));
    }
}
