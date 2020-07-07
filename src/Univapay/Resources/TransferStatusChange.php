<?php

namespace Univapay\Resources;

use DateTime;
use Univapay\Enums\TransferStatus;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\Json\JsonSchema;
use Univapay\Utility\RequesterUtils;

class TransferStatusChange extends Resource
{
    use Jsonable;
    public $id;
    public $merchantId;
    public $transferId;
    public $oldStatus;
    public $newStatus;
    public $reason;
    public $createdOn;

    public function __construct(
        $id,
        $merchantId,
        $transferId,
        TransferStatus $oldStatus,
        TransferStatus $newStatus,
        $reason,
        DateTime $createdOn,
        $context
    ) {
        parent::__construct($id, $context);
        $this->merchantId = $merchantId;
        $this->transferId = $transferId;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->reason = $reason;
        $this->createdOn = $createdOn;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('old_status', true, FormatterUtils::getTypedEnum(TransferStatus::class))
            ->upsert('new_status', true, FormatterUtils::getTypedEnum(TransferStatus::class))
            ->upsert('created_on', true, FormatterUtils::of('getDateTime'));
    }
}
