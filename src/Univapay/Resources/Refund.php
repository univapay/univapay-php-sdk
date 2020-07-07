<?php

namespace Univapay\Resources;

use Univapay\Enums\AppTokenMode;
use Univapay\Enums\RefundReason;
use Univapay\Enums\RefundStatus;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;
use Money\Currency;
use Money\Money;

class Refund extends Resource
{
    use Jsonable;
    use Pollable;

    public $storeId;
    public $chargeId;
    public $status;
    public $currency;
    public $amount;
    public $amountFormatted;
    public $mode;
    public $createdOn;
    public $reason;
    public $message;
    public $error;
    public $metadata;

    public function __construct(
        $id,
        $storeId,
        $chargeId,
        $status,
        $currency,
        $amount,
        $amountFormatted,
        $mode,
        $createdOn,
        $reason = null,
        $message = null,
        $error = null,
        $metadata = null,
        $context = null
    ) {
        parent::__construct($id, $context);
        $this->storeId = $storeId;
        $this->chargeId = $chargeId;
        $this->status = $status;
        $this->currency = $currency;
        $this->amount = $amount;
        $this->amountFormatted = $amountFormatted;
        $this->reason = $reason;
        $this->message = $message;
        $this->error = $error;
        $this->metadata = $metadata;
        $this->mode = $mode;
        $this->createdOn = $createdOn;
    }


    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('status', true, FormatterUtils::getTypedEnum(RefundStatus::class))
            ->upsert('currency', true, FormatterUtils::of('getCurrency'))
            ->upsert('amount', true, FormatterUtils::getMoney('currency'))
            ->upsert('reason', false, FormatterUtils::getTypedEnum(RefundReason::class))
            ->upsert('mode', true, FormatterUtils::getTypedEnum(AppTokenMode::class))
            ->upsert('created_on', true, FormatterUtils::of('getDateTime'));
    }
}
