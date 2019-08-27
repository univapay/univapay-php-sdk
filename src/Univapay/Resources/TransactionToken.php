<?php

namespace Univapay\Resources;

use DateTime;
use DateTimeZone;
use Univapay\Enums\AppTokenMode;
use Univapay\Enums\Field;
use Univapay\Enums\PaymentType;
use Univapay\Enums\Period;
use Univapay\Enums\Reason;
use Univapay\Enums\TokenType;
use Univapay\Enums\UsageLimit;
use Univapay\Errors\UnivapayLogicError;
use Univapay\Errors\UnivapaySDKError;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Resources\Mixins\GetTransactionTokens;
use Univapay\Resources\PaymentData\CardData;
use Univapay\Resources\PaymentData\ConvenienceStoreData;
use Univapay\Resources\PaymentData\PaidyData;
use Univapay\Resources\PaymentData\QRScanData;
use Univapay\Resources\PaymentMethod\PaymentMethodPatch;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\RequesterUtils;
use Univapay\Utility\Json\JsonSchema;
use Money\Money;

class TransactionToken extends Resource
{
    use Jsonable;

    public $storeId;
    public $email;
    public $active;
    public $paymentType;
    public $mode;
    public $type;
    public $usageLimit;
    public $confirmed;
    public $metadata;
    public $createdOn;
    public $lastUsedOn;
    public $data;

    public function __construct(
        $id,
        $storeId,
        $email,
        $active,
        $paymentType,
        $mode,
        $type,
        $usageLimit,
        $confirmed,
        $metadata,
        $createdOn,
        $lastUsedOn,
        $data,
        $context
    ) {
        parent::__construct($id, $context);
        $this->email = $email;
        $this->active = $active;
        $this->storeId = $storeId;
        $this->paymentType = PaymentType::fromValue($paymentType);
        $this->mode = AppTokenMode::fromValue($mode);
        $this->type = TokenType::fromValue($type);
        $this->usageLimit = UsageLimit::fromValue($usageLimit);
        $this->confirmed = $confirmed;
        $this->metadata = $metadata;
        $this->createdOn = date_create($createdOn);
        $this->lastUsedOn = isset($lastUsedOn) ? date_create($lastUsedOn) : null;
        // The payment data may not be available when retrieving from a list. Triggering a ->fetch() will fix this
        $this->data = $data;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('data', false, function ($value, $json) {
                $paymentType = PaymentType::fromValue($json['payment_type']);
                switch ($paymentType) {
                    case PaymentType::CARD():
                    case PaymentType::APPLE_PAY():
                        return CardData::getSchema()->parse($value);
                    case PaymentType::KONBINI():
                        return ConvenienceStoreData::getSchema()->parse($value);
                    case PaymentType::QR_SCAN():
                        return QRScanData::getSchema()->parse($value);
                    case PaymentType::PAIDY():
                        return PaidyData::getSchema()->parse($value);
                }
            });
    }

    protected function getIdContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'tokens', $this->id]);
    }

    public function patch(PaymentMethodPatch $paymentPatch)
    {
        return $this->update($paymentPatch)->fetch();
    }

    public function deactivate()
    {
        return RequesterUtils::executeDelete($this->getIdContext());
    }

    public function createCharge(
        Money $money,
        $capture = null,
        DateTime $captureAt = null,
        array $metadata = null,
        $descriptor = null,
        $onlyDirectCurrency = null
    ) {
        if ($this->type === TokenType::SUBSCRIPTION()) {
            throw new UnivapayLogicError(Reason::NON_SUBSCRIPTION_PAYMENT());
        }
        if (isset($captureAt) && ($captureAt < date_create('+1 hour') || $captureAt > date_create('+7 days'))) {
            throw new UnivapayLogicError(Reason::INVALID_SCHEDULED_CAPTURE_DATE());
        }

        $payload = $money->jsonSerialize() + [
            'transaction_token_id' => $this->id,
            'capture' => $capture,
            'capture_at' => isset($captureAt) ? $captureAt->format(DateTime::ATOM) : null,
            'only_direct_currency' => isset($onlyDirectCurrency) ? 'true' : 'false',
            'descriptor' => $descriptor,
            'metadata' => $metadata
        ];

        $context = $this->context->withPath('charges');
        return RequesterUtils::executePost(Charge::class, $context, FunctionalUtils::stripNulls($payload));
    }

    public function createSubscription(
        Money $money,
        Period $period,
        Money $initialAmount = null,
        ScheduleSettings $scheduleSettings = null,
        InstallmentPlan $installmentPlan = null,
        array $metadata = null
    ) {
        if ($this->type !== TokenType::SUBSCRIPTION()) {
            throw new UnivapayLogicError(Reason::NOT_SUBSCRIPTION_PAYMENT());
        }
        if (!$money->isPositive()) {
            throw new UnivapayValidationError(Field::AMOUNT(), Reason::INVALID_AMOUNT());
        }
        if (isset($initialAmount) && ($initialAmount->isNegative() || !$initialAmount->isSameCurrency($money))) {
            throw new UnivapayValidationError(Field::INITIAL_AMOUNT(), Reason::INVALID_AMOUNT());
        }
        if (isset($scheduleSettings) &&
        $scheduleSettings->preserveEndOfMonth === true &&
        Period::MONTHLY() !== $period) {
            throw new UnivapayValidationError(Field::PRESERVE_END_OF_MONTH(), Reason::MUST_BE_MONTH_BASE_TO_SET());
        }
        
        $payload = $money->jsonSerialize() + [
            'transaction_token_id' => $this->id,
            'period' => $period->getValue(),
            'initial_amount' => isset($initialAmount) ? $initialAmount->getAmount() : null,
            'schedule_settings' => $scheduleSettings,
            'installment_plan' => $installmentPlan,
            'metadata' => $metadata
        ];

        $context = $this->context->withPath('subscriptions');
        return RequesterUtils::executePost(Subscription::class, $context, FunctionalUtils::stripNulls($payload));
    }
}
