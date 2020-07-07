<?php

namespace Univapay\Resources;

use DateTime;
use Money\Currency;
use Money\Money;
use Univapay\Enums\CursorDirection;
use Univapay\Enums\TransferStatus;
use Univapay\Resources\Mixins\GetLedgers;
use Univapay\Resources\Mixins\GetStatusChanges;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\Json\JsonSchema;
use Univapay\Utility\RequesterUtils;

class Transfer extends Resource
{
    use Jsonable;
    use GetLedgers, GetStatusChanges {
        GetLedgers::validate insteadof GetStatusChanges;
    }
    
    public $bankAccountId;
    public $currency;
    public $amount;
    public $amountFormatted;
    public $status;
    public $errorCode;
    public $errorText;
    public $metadata;
    public $note;
    public $from;
    public $to;
    public $createdOn;

    public function __construct(
        $id,
        $bankAccountId,
        Currency $currency,
        Money $amount,
        $amountFormatted,
        TransferStatus $status,
        $errorCode,
        $errorText,
        $metadata,
        $note,
        DateTime $from,
        DateTime $to,
        DateTime $createdOn,
        $context
    ) {
        parent::__construct($id, $context);
        $this->bankAccountId = $bankAccountId;
        $this->currency = $currency;
        $this->amount = $amount;
        $this->amountFormatted = $amountFormatted;
        $this->status = $status;
        $this->errorCode = $errorCode;
        $this->errorText = $errorText;
        $this->metadata = $metadata;
        $this->note = $note;
        $this->from = $from;
        $this->to = $to;
        $this->createdOn = $createdOn;
    }

    protected function getLedgerContext()
    {
        return $this->getIdContext()->appendPath('ledgers');
    }

    protected function getStatusChangeContext()
    {
        return $this->getIdContext()->appendPath('status_changes');
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('currency', true, FormatterUtils::of('getCurrency'))
            ->upsert('amount', true, FormatterUtils::getMoney('currency'))
            ->upsert('status', true, FormatterUtils::getTypedEnum(TransferStatus::class))
            ->upsert('from', true, FormatterUtils::of('getDateTime'))
            ->upsert('to', true, FormatterUtils::of('getDateTime'))
            ->upsert('created_on', true, FormatterUtils::of('getDateTime'));
    }
}
