<?php

namespace Univapay\Resources;

use Univapay\Enums\Reason;
use Univapay\Errors\UnivapaySDKError;

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

        $this->validateAllFieldsExist();
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

    private function validateAllFieldsExist()
    {
        if (is_null($this->authenticationValue) || $this->authenticationValue === '' ||
            is_null($this->eci) || $this->eci === '' ||
            is_null($this->dsTransactionId) || $this->dsTransactionId === '' ||
            is_null($this->serverTransactionId) || $this->serverTransactionId === '' ||
            is_null($this->messageVersion) || $this->messageVersion === '' ||
            is_null($this->transactionStatus) || $this->transactionStatus === '') {
            throw new UnivapaySDKError(Reason::INVALID_THREE_DS_MPI_FIELDS());
        }
    }
}
