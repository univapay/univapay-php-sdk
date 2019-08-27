<?php

namespace Univapay\Resources;

use Univapay\Enums\LedgerOrigin;
use Univapay\Utility\Json\JsonSchema;
use Money\Currency;
use Money\Money;

class Ledger
{
    private static $schema;

    public $id;
    public $storeId;
    public $currency;
    public $amount;
    public $amountFormatted;
    public $percentFee;
    public $flatFeeCurrency;
    public $flatFeeAmount;
    public $flatFeeFormatted;
    public $exchangeRate;
    public $origin;
    public $note;
    public $createdOn;

    public function __construct(
        $id,
        $storeId,
        $currency,
        $amount,
        $amountFormatted,
        $percentFee,
        $flatFeeCurrency,
        $flatFeeAmount,
        $flatFeeFormatted,
        $exchangeRate,
        $origin,
        $note,
        $createdOn
    ) {
        $this->id = $id;
        $this->storeId = $storeId;
        $this->currency = new Currency($currency);
        $this->amount = new Money($amount, $this->currency);
        $this->amountFormatted = $amountFormatted;
        $this->percentFee = $percentFee;
        $this->flatFeeCurrency = new Currency($flatFeeCurrency);
        $this->flatFeeAmount = new Money($flatFeeAmount, $this->flatFeeCurrency);
        $this->flatFeeFormatted = $flatFeeFormatted;
        $this->exchangeRate = $exchangeRate;
        $this->origin = LedgerOrigin::fromValue($origin);
        $this->note = $note;
        $this->createdOn = date_create($createdOn);
    }

    public static function getSchema()
    {
        if (!isset(self::$schema)) {
            self::$schema = JsonSchema::fromClass(self::class);
        }
        return self::$schema;
    }
}
