<?php

namespace Univapay\Resources;

use Univapay\Enums\TransferStatus;
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
        $oldStatus,
        $newStatus,
        $reason,
        $createdOn,
        $context
    ) {
        parent::__construct($id, $context);
        $this->merchantId = $merchantId;
        $this->transferId = $transferId;
        $this->oldStatus = TransferStatus::fromValue($oldStatus);
        $this->newStatus = TransferStatus::fromValue($newStatus);
        $this->reason = $reason;
        $this->createdOn = date_create($createdOn);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
