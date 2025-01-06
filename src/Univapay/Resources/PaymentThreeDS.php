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

    /**
     * Initializes a PaymentThreeDS object with the specified redirect endpoint
     * and 3DS mode. The user will be redirected to the provided endpoint after completing
     * 3DS authentication.
     *
     * @param string $redirectEndpoint The URL to which the user will be redirected after 3DS authentication.
     * @param ThreeDSMode $mode The 3DS mode to be used for the authentication process.
     */
    public static function withThreeDS($redirectEndpoint, ThreeDSMode $mode)
    {
        return new self($redirectEndpoint, $mode);
    }

    /**
     * 3DS authentication with a provided Merchant Plug-In (MPI).
     *
    * @param string $authenticationValue length of 28 characters.
    * @param string $eci length of 2 characters.
    * @param string $dsTransactionId should be a UUID.
    * @param string $serverTransactionId should be a UUID.
    * @param string $messageVersion e.g., "2.1.0" or "2.2.0".
    * @param string $transactionStatus e.g., "Y" or "A".
     */
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
