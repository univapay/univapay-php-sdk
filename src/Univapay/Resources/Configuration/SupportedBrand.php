<?php

namespace Univapay\Resources\Configuration;

use Univapay\Enums\CardBrand;
use Univapay\Enums\OnlineBrand;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\FunctionalUtils as fp;
use Univapay\Utility\Json\JsonSchema;

class SupportedBrand
{
    use Jsonable;

    public $supportAuthCapture;
    public $requiresFullName;
    public $requiresCvv;
    public $supportedCurrencies;
    public $countriesAllowed;
    public $cardBrand;
    public $onlineBrand;

    public function __construct(
        $supportAuthCapture,
        $requiresFullName,
        $requiresCvv,
        array $supportedCurrencies,
        array $countriesAllowed = null,
        CardBrand $cardBrand = null,
        OnlineBrand $onlineBrand = null
    ) {
        $this->supportAuthCapture = $supportAuthCapture;
        $this->requiresFullName = $requiresFullName;
        $this->requiresCvv = $requiresCvv;
        $this->supportedCurrencies = $supportedCurrencies;
        $this->countriesAllowed = $countriesAllowed;
        $this->cardBrand = $cardBrand;
        $this->onlineBrand = $onlineBrand;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('supported_currencies', false, FormatterUtils::getListOf(FormatterUtils::of('getCurrency')))
            ->upsert('countries_allowed', false, FormatterUtils::getListOf())
            ->upsert('card_brand', false, FormatterUtils::getTypedEnum(CardBrand::class))
            ->upsert('online_brand', false, FormatterUtils::getTypedEnum(OnlineBrand::class));
    }
}
