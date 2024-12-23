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
    public $mode;
    public $threeDSMPI;
    public $redirectId;

    public function __construct(
        $redirectEndpoint = null,
        ThreeDSMode $mode = null,
        ThreeDSMPI $threeDSMPI = null,
        $redirectId = null
    ) {
        $this->redirectEndpoint = $redirectEndpoint;
        $this->mode = $mode;
        $this->threeDSMPI = $threeDSMPI;
        $this->redirectId = $redirectId;
    }

    public static function withThreeDS($redirectEndpoint, ThreeDSMode $mode)
    {
        return new self($redirectEndpoint, $mode);
    }

    public static function withThreeDSMPI(
        $authenticationValue,
        $eci,
        $dsTransactionId,
        $serverTransactionId,
        $messageVersion,
        $transactionStatus
    ) {
        return new self(null, null, new ThreeDSMPI(
            $authenticationValue,
            $eci,
            $dsTransactionId,
            $serverTransactionId,
            $messageVersion,
            $transactionStatus
        ));
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
            'mode' =>  isset($this->mode) ? $this->mode->getValue() : null,
        ]) + (isset($this->threeDSMPI) ? $this->threeDSMPI->jsonSerialize() : []);
    }
}
