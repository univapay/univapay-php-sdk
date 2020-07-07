<?php

namespace Univapay\Resources;

use DateTime;
use Money\Currency;
use Univapay\Enums\BankAccountStatus;
use Univapay\Enums\BankAccountType;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

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
        Currency $currency,
        $accountNumber,
        $routingNumber,
        $swiftCode,
        $ifscCode,
        $routingCode,
        $lastFour,
        BankAccountStatus $status,
        BankAccountType $accountType,
        DateTime $createdOn,
        $context
    ) {
        parent::__construct($id, $context);
        $this->primary = $primary;
        $this->holderName = $holderName;
        $this->bankName = $bankName;
        $this->branchName = $branchName;
        $this->country = $country;
        $this->bankAddress = $bankAddress;
        $this->currency = $currency;
        $this->accountNumber = $accountNumber;
        $this->routingNumber = $routingNumber;
        $this->swiftCode = $swiftCode;
        $this->ifscCode = $ifscCode;
        $this->routingCode = $routingCode;
        $this->lastFour = $lastFour;
        $this->status = $status;
        $this->accountType = $accountType;
        $this->createdOn = $createdOn;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('currency', true, FormatterUtils::of('getCurrency'))
            ->upsert('status', true, FormatterUtils::getTypedEnum(BankAccountStatus::class))
            ->upsert('account_type', true, FormatterUtils::getTypedEnum(BankAccountType::class))
            ->upsert('created_on', true, FormatterUtils::of('getDateTime'));
    }
}
