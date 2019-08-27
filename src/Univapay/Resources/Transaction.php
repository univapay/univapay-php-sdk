<?php

namespace Univapay\Resources;

use Univapay\Enums\AppTokenMode;
use Univapay\Enums\ChargeStatus;
use Univapay\Enums\TransactionType;
use Univapay\Utility\Json\JsonSchema;
use Money\Currency;
use Money\Money;

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
        $currency,
        $amount,
        $amountFormatted,
        $type,
        $status,
        $metadata,
        $mode,
        $userData,
        $createdOn,
        $context
    ) {
        $this->id = $id;
        $this->storeId = $storeId;
        $this->resourceId = $resourceId;
        $this->chargeId = $chargeId;
        $this->currency = new Currency($currency);
        $this->amount = new Money($amount, $this->currency);
        $this->amountFormatted = $amountFormatted;
        $this->type = TransactionType::fromValue($type);
        $this->status = ChargeStatus::fromValue($status);
        $this->metadata = $metadata;
        $this->mode = AppTokenMode::fromValue($mode);
        $this->userData = $userData;
        $this->createdOn = date_create($createdOn);
        $this->context = $context;
    }


    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
