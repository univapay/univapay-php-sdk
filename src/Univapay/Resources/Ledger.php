<?php

namespace Univapay\Resources;

use DateTime;
use Money\Currency;
use Money\Money;
use Univapay\Enums\LedgerOrigin;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class Ledger extends Resource
{
    use Jsonable;

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
        Currency $currency,
        Money $amount,
        $amountFormatted,
        $percentFee,
        Currency $flatFeeCurrency,
        Money $flatFeeAmount,
        $flatFeeFormatted,
        $exchangeRate,
        LedgerOrigin $origin,
        $note,
        DateTime $createdOn
    ) {
        $this->id = $id;
        $this->storeId = $storeId;
        $this->currency = $currency;
        $this->amount = $amount;
        $this->amountFormatted = $amountFormatted;
        $this->percentFee = $percentFee;
        $this->flatFeeCurrency = $flatFeeCurrency;
        $this->flatFeeAmount = $flatFeeAmount;
        $this->flatFeeFormatted = $flatFeeFormatted;
        $this->exchangeRate = $exchangeRate;
        $this->origin = $origin;
        $this->note = $note;
        $this->createdOn = $createdOn;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('currency', true, FormatterUtils::of('getCurrency'))
            ->upsert('amount', true, FormatterUtils::getMoney('currency'))
            ->upsert('flat_fee_currency', true, FormatterUtils::of('getCurrency'))
            ->upsert('flat_fee_amount', true, FormatterUtils::getMoney('flat_fee_currency'))
            ->upsert('origin', true, FormatterUtils::getTypedEnum(LedgerOrigin::class))
            ->upsert('created_on', true, FormatterUtils::of('getDateTime'));
    }
}
