<?php
namespace Univapay\Resources\PaymentToken;

use Univapay\Resources\Jsonable;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\Json\JsonSchema;

class OnlineToken
{
    use Jsonable;

    public $issuerToken;

    public function __construct(
        $issuerToken = null
    ) {
        $this->issuerToken = $issuerToken;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
