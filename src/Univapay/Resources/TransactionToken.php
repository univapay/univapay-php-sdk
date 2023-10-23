<?php

namespace Univapay\Resources;

use DateInterval;
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
use Univapay\Resources\PaymentData\OnlineData;
use Univapay\Resources\PaymentData\PaidyData;
use Univapay\Resources\PaymentData\QrMerchantData;
use Univapay\Resources\PaymentData\QrScanData;
use Univapay\Resources\PaymentMethod\PaymentMethodPatch;
use Univapay\Resources\Subscription\InstallmentPlan;
use Univapay\Resources\Subscription\ScheduleSettings;
use Univapay\Resources\Subscription\SubscriptionPlan;
use Univapay\Utility\DateUtils;
use Univapay\Utility\FormatterUtils;
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
    public $confirmed;
    public $createdOn;
    public $data;
    public $metadata;
    public $usageLimit;
    public $lastUsedOn;
    public $ipAddress;

    public function __construct(
        $id,
        $storeId,
        $email,
        $active,
        PaymentType $paymentType,
        AppTokenMode $mode,
        TokenType $type,
        $confirmed,
        DateTime $createdOn,
        $data,
        $metadata = null,
        UsageLimit $usageLimit = null,
        DateTime $lastUsedOn = null,
        $ipAddress = null,
        $context = null
    ) {
        parent::__construct($id, $context);
        $this->email = $email;
        $this->active = $active;
        $this->storeId = $storeId;
        $this->paymentType = $paymentType;
        $this->mode = $mode;
        $this->type = $type;
        $this->confirmed = $confirmed;
        $this->metadata = $metadata;
        $this->createdOn = $createdOn;
        $this->usageLimit = $usageLimit;
        $this->lastUsedOn = $lastUsedOn;
        $this->ipAddress = $ipAddress;
        // The payment data may not be available when retrieving from a list. Triggering a ->fetch() will fix this
        $this->data = $data;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('payment_type', true, FormatterUtils::getTypedEnum(PaymentType::class))
            ->upsert('mode', true, FormatterUtils::getTypedEnum(AppTokenMode::class))
            ->upsert('type', true, FormatterUtils::getTypedEnum(TokenType::class))
            ->upsert('created_on', true, FormatterUtils::of('getDateTime'))
            ->upsert('usage_limit', false, FormatterUtils::getTypedEnum(UsageLimit::class))
            ->upsert('last_used_on', false, FormatterUtils::of('getDateTime'))
            ->upsert('data', false, function ($value, $json) {
                $paymentType = PaymentType::fromValue($json['payment_type']);
                switch ($paymentType) {
                    case PaymentType::CARD():
                    case PaymentType::APPLE_PAY():
                        return CardData::getSchema()->parse($value);
                    case PaymentType::KONBINI():
                        return ConvenienceStoreData::getSchema()->parse($value);
                    case PaymentType::QR_SCAN():
                        return QrScanData::getSchema()->parse($value);
                    case PaymentType::QR_MERCHANT():
                        return QrMerchantData::getSchema()->parse($value);
                    case PaymentType::PAIDY():
                        return PaidyData::getSchema()->parse($value);
                    case PaymentType::ONLINE():
                        return OnlineData::getSchema()->parse($value);
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
        $onlyDirectCurrency = null
    ) {
        if ($this->type === TokenType::SUBSCRIPTION()) {
            throw new UnivapayLogicError(Reason::NON_SUBSCRIPTION_PAYMENT());
        }
        $this->validateCapture($capture, $captureAt);

        $payload = $money->jsonSerialize() + [
            'transaction_token_id' => $this->id,
            'metadata' => $metadata
        ] + (isset($capture)
            ? ['capture' => $capture]
            : []
        ) + (isset($captureAt)
            ? ['capture_at' => $captureAt->format(DateTime::ATOM)]
            : []
        ) + (isset($onlyDirectCurrency)
            ? ['only_direct_currency' => $onlyDirectCurrency]
            : []
        );

        $context = $this->context->withPath('charges');
        return RequesterUtils::executePost(Charge::class, $context, FunctionalUtils::stripNulls($payload));
    }

    public function createSubscription(
        Money $money,
        Period $period = null,
        Money $initialAmount = null,
        ScheduleSettings $scheduleSettings = null,
        SubscriptionPlan $subscriptionPlan = null,
        InstallmentPlan $installmentPlan = null,
        array $metadata = null,
        $onlyDirectCurrency = null,
        $firstChargeAuthorizationOnly = null,
        DateInterval $firstChargeCaptureAfter = null,
        DateInterval $cyclicalPeriod = null
    ) {
        if ($this->type !== TokenType::SUBSCRIPTION()) {
            throw new UnivapayLogicError(Reason::NOT_SUBSCRIPTION_PAYMENT());
        }
        if (!isset($period) && !isset($cyclicalPeriod)) {
            throw new UnivapayValidationError(Field::PERIOD(), Reason::PERIOD_OR_CYCLICAL_PERIOD_MUST_BE_SET());
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
        $this->validateCapture($firstChargeAuthorizationOnly, null, $firstChargeCaptureAfter);
        
        $payload = $money->jsonSerialize() + [
            'transaction_token_id' => $this->id,
            'period' => isset($period) ? $period->getValue() : null,
            'cyclical_period' => isset($cyclicalPeriod) ? DateUtils::asPeriodString($cyclicalPeriod) : null,
            'initial_amount' => isset($initialAmount) ? $initialAmount->getAmount() : null,
            'schedule_settings' => isset($scheduleSettings) ? $scheduleSettings->jsonSerialize() : null,
            'subscription_plan' => isset($subscriptionPlan) ? $subscriptionPlan->jsonSerialize() : null,
            'installment_plan' => isset($installmentPlan) ? $installmentPlan->jsonSerialize() : null,
            'metadata' => $metadata
        ] + (isset($firstChargeAuthorizationOnly)
            ? ['first_charge_authorization_only' => $firstChargeAuthorizationOnly]
            : []
        ) + (isset($firstChargeCaptureAfter)
            ? ['first_charge_capture_after' => DateUtils::asPeriodString($firstChargeCaptureAfter)]
            : []
        ) + (isset($onlyDirectCurrency)
            ? ['only_direct_currency' => $onlyDirectCurrency]
            : []
        );

        $context = $this->context->withPath('subscriptions');
        return RequesterUtils::executePost(Subscription::class, $context, FunctionalUtils::stripNulls($payload));
    }

    private function validateCapture(
        $capture = null,
        DateTime $captureAtAbsolute = null,
        DateInterval $captureAtRelative = null
    ) {
        if (isset($captureAtRelative)) {
            $captureAtAbsolute = date_create()->add($captureAtRelative);
        }
        if (isset($capture)) {
            if ($this->paymentType !== PaymentType::CARD() &&
                $this->paymentType !== PaymentType::APPLE_PAY() &&
                $this->paymentType !== PaymentType::PAIDY()) {
                throw new UnivapayLogicError(Reason::CAPTURE_ONLY_FOR_CARD_PAYMENT());
            }
        }
    }
}
