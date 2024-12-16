<?php

namespace Univapay\Resources;

use Univapay\Errors\UnivapaySDKError;
use Univapay\Enums\Reason;
use Univapay\Errors\UnivapayError;
use Univapay\Errors\UnivapayRequestError;

class ThreeDSMPI
{
    public $authenticationValue;
    public $eci;
    public $dsTransactionId;
    public $serverTransactionId;
    public $messageVersion;
    public $transactionStatus;

    public function __construct(
        $authenticationValue = null,
        $eci = null,
        $dsTransactionId = null,
        $serverTransactionId = null,
        $messageVersion = null,
        $transactionStatus = null
    ) {
        $this->authenticationValue = $authenticationValue;
        $this->eci = $eci;
        $this->dsTransactionId = $dsTransactionId;
        $this->serverTransactionId = $serverTransactionId;
        $this->messageVersion = $messageVersion;
        $this->transactionStatus = $transactionStatus;

        $this->validate();
    }

    private function validate()
    {
        $allNullOrEmpty = $this->isAllNullOrEmpty();

        // not using 3DS MPI
        if ($allNullOrEmpty) {
            return;
        }

        $this->validateAuthenticationValue();
        $this->validateEci();
        $this->validateDsTransactionId();
        $this->validateServerTransactionId();
        $this->validateMessageVersion();
        $this->validateTransactionStatus();
    }

    private function isAllNullOrEmpty()
    {
        return (is_null($this->authenticationValue) || $this->authenticationValue === '') &&
            (is_null($this->eci) || $this->eci === '') &&
            (is_null($this->dsTransactionId) || $this->dsTransactionId === '') &&
            (is_null($this->serverTransactionId) || $this->serverTransactionId === '') &&
            (is_null($this->messageVersion) || $this->messageVersion === '') &&
            (is_null($this->transactionStatus) || $this->transactionStatus === '');
    }

    private function validateAuthenticationValue()
    {
        if (strlen($this->authenticationValue) != 28) {
            throw new UnivapaySDKError(Reason::INVALID_THREE_DS_MPI_AUTHENTICATION_LENGTH());
        }
    }

    private function validateEci()
    {
        if (strlen($this->eci) != 2) {
            throw new UnivapaySDKError(Reason::INVALID_THREE_DS_MPI_ECI_LENGTH());
        }
    }

    private function validateUUID(string $value) {
        if (!preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $value)) {
            throw new UnivapaySDKError(Reason::INVALID_THREE_DS_MPI_UUID_FORMAT());
        }
    }

    private function validateDsTransactionId()
    {
        if (is_null($this->dsTransactionId) || $this->dsTransactionId === '') {
            throw new UnivapaySDKError(Reason::INVALID_THREE_DS_MPI_DS_TRANSACTION_ID());
        }

        $this->validateUUID($this->dsTransactionId);
    }

    private function validateServerTransactionId()
    {
        if (is_null($this->serverTransactionId) || $this->serverTransactionId === '') {
            throw new UnivapaySDKError(Reason::INVALID_THREE_DS_MPI_SERVER_TRANSACTION_ID());
        }

        $this->validateUUID($this->serverTransactionId);
    }

    private function validateMessageVersion()
    {
        if (is_null($this->messageVersion) || $this->messageVersion === '' ||
            !in_array($this->messageVersion, ["2.1.0", "2.2.0"])) {
            throw new UnivapaySDKError(Reason::INVALID_THREE_DS_MPI_MESSAGE_VERSION());
        }
    }

    private function validateTransactionStatus()
    {
        if (is_null($this->transactionStatus) || $this->transactionStatus === '' ||
            !in_array($this->transactionStatus, ["Y", "A"])) {
            throw new UnivapaySDKError(Reason::INVALID_THREE_DS_MPI_TRANSACTION_STATUS());
        }
    }
}
