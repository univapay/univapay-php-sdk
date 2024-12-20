<?php

namespace Univapay\Resources;

use Univapay\Enums\ThreeDSMode;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class PaymentThreeDS
{
    use Jsonable;

    public $redirectEndpoint;
    public $redirectId;
    public $mode;
    public $threeDSMPI;

    public function __construct(
        $redirectEndpoint = null,
        $redirectId = null,
        ThreeDSMode $mode = null,
        ThreeDSMPI $threeDSMPI = null
    ) {
        $this->redirectEndpoint = $redirectEndpoint;
        $this->redirectId = $redirectId;
        $this->mode = $mode;
        $this->threeDSMPI = $threeDSMPI;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('mode', false, FormatterUtils::getTypedEnum(ThreeDSMode::class));
    }

    public function jsonSerialize()
    {
        return FunctionalUtils::stripNulls([
            'redirect_endpoint' => $this->redirectEndpoint,
            'mode' =>  $this->mode ? $this->mode->getValue() : null,
            'authentication_value' => $this->threeDSMPI ? ($this->threeDSMPI->authenticationValue ?? null) : null,
            'eci' => $this->threeDSMPI ? ($this->threeDSMPI->eci ?? null) : null,
            'ds_transaction_id' => $this->threeDSMPI ? ($this->threeDSMPI->dsTransactionId ?? null) : null,
            'server_transaction_id' => $this->threeDSMPI ? ($this->threeDSMPI->serverTransactionId ?? null) : null,
            'message_version' => $this->threeDSMPI ? ($this->threeDSMPI->messageVersion ?? null) : null,
            'transaction_status' => $this->threeDSMPI ? ($this->threeDSMPI->transactionStatus ?? null) : null
        ]);
    }
}
