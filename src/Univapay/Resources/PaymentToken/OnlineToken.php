<?php
namespace Univapay\Resources\PaymentToken;

use Univapay\Enums\CallMethod;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\Json\JsonSchema;

class OnlineToken
{
    use Jsonable;

    public $issuerToken;
    public $callMethod;

    public function __construct(
        $issuerToken = null,
        CallMethod $callMethod = null
    ) {
        $this->issuerToken = $issuerToken;
        $this->callMethod = $callMethod;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('call_method', true, FormatterUtils::getTypedEnum(CallMethod::class));
    }
}
