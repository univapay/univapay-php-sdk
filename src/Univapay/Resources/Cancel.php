<?php

namespace Univapay\Resources;

use Univapay\Enums\AppTokenMode;
use Univapay\Enums\CancelStatus;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class Cancel extends Resource
{
    use Jsonable;
    use Pollable;

    public $chargeId;
    public $storeId;
    public $status;
    public $error;
    public $metadata;
    public $mode;
    public $createdOn;

    public function __construct($id, $chargeId, $storeId, $status, $error, $metadata, $mode, $createdOn, $context)
    {
        parent::__construct($id, $context);
        $this->chargeId = $chargeId;
        $this->storeId = $storeId;
        $this->status = $status;
        $this->error = $error;
        $this->metadata = $metadata;
        $this->mode = $mode;
        $this->createdOn = $createdOn;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
        ->upsert('status', true, FormatterUtils::getTypedEnum(CancelStatus::class))
        ->upsert('mode', true, FormatterUtils::getTypedEnum(AppTokenMode::class))
        ->upsert('created_on', true, FormatterUtils::of('getDateTime'));
    }
    
    protected function getIdContext()
    {
        return $this->context->withPath(
            ['stores', $this->storeId, 'charges', $this->chargeId, 'cancels', $this->id]
        );
    }
    
    protected function pollableStatuses()
    {
        return [(string) CancelStatus::PENDING() => array_diff(CancelStatus::findValues(), [CancelStatus::PENDING()])];
    }

    protected function statusPropertyPath()
    {
        return 'status';
    }
}
