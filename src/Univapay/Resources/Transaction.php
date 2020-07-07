<?php

namespace Univapay\Resources;

use DateTime;
use Money\Currency;
use Money\Money;
use Univapay\Enums\AppTokenMode;
use Univapay\Enums\ChargeStatus;
use Univapay\Enums\TransactionType;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class Transaction
{
    use Jsonable;

    public $id;
    public $storeId;
    public $resourceId;
    public $chargeId;
    public $currency;
    public $amount;
    public $amountFormatted;
    public $type;
    public $status;
    public $metadata;
    public $mode;
    public $userData;
    public $createdOn;
    private $context;

    public function __construct(
        $id,
        $storeId,
        $resourceId,
        $chargeId,
        Currency $currency,
        Money $amount,
        $amountFormatted,
        TransactionType $type,
        ChargeStatus $status,
        $metadata,
        AppTokenMode $mode,
        $userData,
        DateTime $createdOn,
        $context
    ) {
        $this->id = $id;
        $this->storeId = $storeId;
        $this->resourceId = $resourceId;
        $this->chargeId = $chargeId;
        $this->currency = $currency;
        $this->amount = $amount;
        $this->amountFormatted = $amountFormatted;
        $this->type = $type;
        $this->status = $status;
        $this->metadata = $metadata;
        $this->mode = $mode;
        $this->userData = $userData;
        $this->createdOn = $createdOn;
        $this->context = $context;
    }


    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
        ->upsert('currency', true, FormatterUtils::of('getCurrency'))
        ->upsert('amount', true, FormatterUtils::getMoney('currency'))
        ->upsert('type', true, FormatterUtils::getTypedEnum(TransactionType::class))
        ->upsert('status', true, FormatterUtils::getTypedEnum(ChargeStatus::class))
        ->upsert('mode', true, FormatterUtils::getTypedEnum(AppTokenMode::class))
        ->upsert('created_on', true, FormatterUtils::of('getDateTime'));
    }
}
