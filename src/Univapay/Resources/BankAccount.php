<?php

namespace Univapay\Resources;

use Univapay\Enums\BankAccountStatus;
use Univapay\Enums\BankAccountType;
use Univapay\Utility\Json\JsonSchema;
use Money\Currency;

class BankAccount extends Resource
{
    use Jsonable;

    public $primary;
    public $holderName;
    public $bankName;
    public $branchName;
    public $country;
    public $bankAddress;
    public $currency;
    public $accountNumber;
    public $routingNumber;
    public $swiftCode;
    public $ifscCode;
    public $routingCode;
    public $lastFour;
    public $status;
    public $accountType;
    public $createdOn;

    public function __construct(
        $id,
        $primary,
        $holderName,
        $bankName,
        $branchName,
        $country,
        $bankAddress,
        $currency,
        $accountNumber,
        $routingNumber,
        $swiftCode,
        $ifscCode,
        $routingCode,
        $lastFour,
        $status,
        $accountType,
        $createdOn,
        $context
    ) {
        parent::__construct($id, $context);
        $this->primary = $primary;
        $this->holderName = $holderName;
        $this->bankName = $bankName;
        $this->branchName = $branchName;
        $this->country = $country;
        $this->bankAddress = $bankAddress;
        $this->currency = new Currency($currency);
        $this->accountNumber = $accountNumber;
        $this->routingNumber = $routingNumber;
        $this->swiftCode = $swiftCode;
        $this->ifscCode = $ifscCode;
        $this->routingCode = $routingCode;
        $this->lastFour = $lastFour;
        $this->status = BankAccountStatus::fromValue($status);
        $this->accountType = BankAccountType::fromValue($accountType);
        $this->createdOn = date_create($createdOn);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
