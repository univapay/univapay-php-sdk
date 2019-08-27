<?php

namespace Univapay\Resources\Configuration;

use Univapay\Resources\Jsonable;
use Univapay\Utility\FunctionalUtils as fp;
use Univapay\Utility\Json\JsonSchema;

class CardConfiguration
{
    use Jsonable;

    public $enabled;
    public $debitEnabled;
    public $prepaidEnabled;
    public $onlyDirectCurrency;
    public $forbiddenCardBrands;
    public $allowedCountriesByIp;
    public $foreignCardsAllowed;
    public $failOnNewEmail;
    public $cardLimit;
    public $allowEmptyCvv;

    public function __construct(
        $enabled,
        $debitEnabled,
        $prepaidEnabled,
        $onlyDirectCurrency,
        $forbiddenCardBrands,
        $allowedCountriesByIp,
        $foreignCardsAllowed,
        $failOnNewEmail,
        $cardLimit,
        $allowEmptyCvv
    ) {
        $this->enabled = $enabled;
        $this->debitEnabled = $debitEnabled;
        $this->prepaidEnabled = $prepaidEnabled;
        $this->onlyDirectCurrency = $onlyDirectCurrency;
        $this->forbiddenCardBrands = $forbiddenCardBrands;
        $this->allowedCountriesByIp = $allowedCountriesByIp;
        $this->foreignCardsAllowed = $foreignCardsAllowed;
        $this->failOnNewEmail = $failOnNewEmail;
        $this->cardLimit = $cardLimit;
        $this->allowEmptyCvv = $allowEmptyCvv;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
