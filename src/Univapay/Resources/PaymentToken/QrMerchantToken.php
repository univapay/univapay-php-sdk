<?php
namespace Univapay\Resources\PaymentToken;

use Univapay\Resources\Jsonable;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\Json\JsonSchema;

class QrMerchantToken
{
    use Jsonable;

    public $ready;
    public $qrImageUrl;

    public function __construct(
        $ready,
        $qrImageUrl = null
    ) {
        $this->ready = $ready;
        $this->qrImageUrl = $qrImageUrl;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
