<?php

namespace Univapay\Resources;

use Univapay\Enums\ThreeDSMode;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class PaymentThreeDS extends ThreeDSMPI
{
    use Jsonable;

    public $redirectEndpoint;
    public $mode;
    public $threeDSMPI;
    public $redirectId;

    /**
     * PaymentThreeDS constructor.
     *
     * @param string $redirectEndpoint
     * @param ThreeDSMode $mode Acceptable values: "FORCE", "IF_AVAILABLE", "NORMAL", "PROVIDED", "REQUIRE", "SKIP"
     * @param null $redirectId
     */
    public function __construct(
        $redirectEndpoint,
        $mode,
        ThreeDSMPI $threeDSMPI = null,
        $redirectId = null
    ) {
        $this->redirectEndpoint = $redirectEndpoint;
        $this->mode = $mode;
        $this->threeDSMPI = $threeDSMPI;
        $this->redirectId = $redirectId;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('mode', false, FormatterUtils::getTypedEnum(ThreeDSMode::class));
    }

    public function jsonSerialize()
    {
        return [
            'redirect_endpoint' => $this->redirectEndpoint,
            'mode' => $this->mode,
            'authentication_value' => $this->threeDSMPI->authenticationValue,
            'eci' => $this->threeDSMPI->eci,
            'ds_transaction_id' => $this->threeDSMPI->dsTransactionId,
            'server_transaction_id' => $this->threeDSMPI->serverTransactionId,
            'message_version' => $this->threeDSMPI->messageVersion,
            'transaction_status' => $this->threeDSMPI->transactionStatus
        ];
    }
}
