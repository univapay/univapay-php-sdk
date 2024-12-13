<?php

namespace Univapay\Resources;

use Univapay\Resources\Jsonable;
use Univapay\Errors\UnivapaySDKError;
use Univapay\Enums\Reason;

class ThreeDSMPI
{
    use Jsonable;

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
        $allNullOrEmpty = (is_null($this->authenticationValue) || $this->authenticationValue === '') &&
            (is_null($this->eci) || $this->eci === '') &&
            (is_null($this->dsTransactionId) || $this->dsTransactionId === '') &&
            (is_null($this->serverTransactionId) || $this->serverTransactionId === '') &&
            (is_null($this->messageVersion) || $this->messageVersion === '') &&
            (is_null($this->transactionStatus) || $this->transactionStatus === '');

        // not using 3DS MPI
        if ($allNullOrEmpty) {
            return;
        }

        if (!$this->authenticationValue || strlen($this->authenticationValue) != 28) {
            throw new UnivapaySDKError(Reason::INVALID_THREE_DS_MPI_AUTHENTICATION_LENGTH());
        }

        if (!$this->eci || strlen($this->eci) != 2) {
            throw new UnivapaySDKError(Reason::INVALID_THREE_DS_MPI_ECI_LENGTH());
        }

        if (!$this->dsTransactionId) {
            throw new UnivapaySDKError(Reason::INVALID_THREE_DS_MPI_DS_TRANSACTION_ID());
        }

        if (!$this->serverTransactionId) {
            throw new UnivapaySDKError(Reason::INVALID_THREE_DS_MPI_SERVER_TRANSACTION_ID());
        }

        if (!$this->messageVersion || !in_array($this->messageVersion, ["2.1.0", "2.2.0"])) {
            throw new UnivapaySDKError(Reason::INVALID_THREE_DS_MPI_MESSAGE_VERSION());
        }

        if (!$this->transactionStatus || !in_array($this->transactionStatus, ["Y", "A"])) {
            throw new UnivapaySDKError(Reason::INVALID_THREE_DS_MPI_TRANSACTION_STATUS());
        }
    }
}
