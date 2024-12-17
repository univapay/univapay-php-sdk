<?php

namespace Univapay\Resources;

use DateInterval;
use DateTime;
use Money\Currency;
use Money\Money;
use Univapay\Enums\AppTokenMode;
use Univapay\Enums\Field;
use Univapay\Enums\PaymentType;
use Univapay\Enums\Period;
use Univapay\Enums\Reason;
use Univapay\Enums\SubscriptionStatus;
use Univapay\Errors\UnivapayLogicError;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Resources\Mixins\GetCharges;
use Univapay\Resources\Mixins\GetScheduledPayments;
use Univapay\Resources\Subscription\InstallmentPlan;
use Univapay\Resources\Subscription\ScheduledPayment;
use Univapay\Resources\Subscription\ScheduleSettings;
use Univapay\Resources\Subscription\SubscriptionPlan;
use Univapay\Utility\DateUtils;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\RequesterUtils;
use Univapay\Utility\Json\JsonSchema;

class Subscription extends Resource
{
    use Jsonable;
    use Pollable;
    use GetCharges, GetScheduledPayments {
        GetCharges::validate insteadof GetScheduledPayments;
    }

    public $storeId;
    public $transactionTokenId;
    public $currency;
    public $amount;
    public $amountFormatted;
    public $period;
    public $cyclicalPeriod;
    public $scheduleSettings;
    public $paymentsLeft;
    public $status;
    public $metadata;
    public $mode;
    public $createdOn;
    public $amountLeft;
    public $amountLeftFormatted;
    public $initialAmount;
    public $initialAmountFormatted;
    public $nextPayment;
    public $subscriptionPlan;
    public $installmentPlan;
    public $firstChargeAuthorizationOnly;
    public $firstChargeCaptureAfter;

    public function __construct(
        $id,
        $storeId,
        $transactionTokenId,
        Currency $currency,
        Money $amount,
        $amountFormatted,
        Period $period = null,
        DateInterval $cyclicalPeriod = null,
        ScheduleSettings $scheduleSettings,
        $paymentsLeft,
        SubscriptionStatus $status,
        $metadata,
        AppTokenMode $mode,
        DateTime $createdOn,
        Money $amountLeft = null,
        $amountLeftFormatted,
        Money $initialAmount = null,
        $initialAmountFormatted = null,
        ScheduledPayment $nextPayment = null,
        SubscriptionPlan $subscriptionPlan = null,
        InstallmentPlan $installmentPlan = null,
        $firstChargeAuthorizationOnly = null,
        DateInterval $firstChargeCaptureAfter = null,
        $context = null
    ) {
        parent::__construct($id, $context);
        $this->storeId = $storeId;
        $this->transactionTokenId = $transactionTokenId;
        $this->currency = $currency;
        $this->amount = $amount;
        $this->amountFormatted = $amountFormatted;
        $this->period = $period;
        $this->cyclicalPeriod = $cyclicalPeriod;
        $this->initialAmount = $initialAmount;
        $this->initialAmountFormatted = $initialAmountFormatted;
        $this->scheduleSettings = $scheduleSettings;
        $this->nextPayment = $nextPayment;
        $this->paymentsLeft = $paymentsLeft;
        $this->amountLeft = $amountLeft;
        $this->amountLeftFormatted = $amountLeftFormatted;
        $this->status = $status;
        $this->metadata = $metadata;
        $this->mode = $mode;
        $this->createdOn = $createdOn;
        $this->subscriptionPlan = $subscriptionPlan;
        $this->installmentPlan = $installmentPlan;
        $this->firstChargeAuthorizationOnly = $firstChargeAuthorizationOnly;
        $this->firstChargeCaptureAfter = $firstChargeCaptureAfter;
    }

    public function patch(
        $transactionTokenId = null,
        Money $initialAmount = null,
        Period $period = null,
        ScheduleSettings $scheduleSettings = null,
        SubscriptionStatus $status = null,
        array $metadata = null,
        SubscriptionPlan $subscriptionPlan = null,
        InstallmentPlan $installmentPlan = null,
        DateInterval $cyclicalPeriod = null
    ) {
        if (SubscriptionStatus::CANCELED() == $this->status) {
            throw new UnivapayLogicError(Reason::CANNOT_CHANGE_CANCELED_SUBSCRIPTION());
        }
        if (isset($transactionTokenId) && !$this->isTokenPatchable()) {
            throw new UnivapayLogicError(Reason::CANNOT_CHANGE_TOKEN());
        }
        if (isset($initialAmount) && !$this->isEditable() && $initialAmount->isNegative()) {
            throw new UnivapayValidationError(Field::INITIAL_AMOUNT(), Reason::INVALID_FORMAT());
        }
        if (isset($period) && !$this->isEditable()) {
            throw new UnivapayLogicError(Reason::CANNOT_SET_AFTER_SUBSCRIPTION_STARTED());
        }
        if (isset($cyclicalPeriod) && !$this->isEditable()) {
            throw new UnivapayLogicError(Reason::CANNOT_SET_AFTER_SUBSCRIPTION_STARTED());
        }
        if (isset($status)) {
            switch ($this->status) {
                case SubscriptionStatus::UNPAID():
                case SubscriptionStatus::CURRENT():
                    if (SubscriptionStatus::SUSPENDED() !== $status) {
                        throw new UnivapayValidationError(Field::STATUS(), Reason::FORBIDDEN_PARAMETER());
                    }
                    break;
                case SubscriptionStatus::SUSPENDED():
                    if (SubscriptionStatus::UNPAID() !== $status) {
                        throw new UnivapayValidationError(Field::STATUS(), Reason::FORBIDDEN_PARAMETER());
                    }
                    break;
                default:
                    throw new UnivapayValidationError(Field::STATUS(), Reason::FORBIDDEN_PARAMETER());
            }
        }
        if ((isset($subscriptionPlan) || isset($installmentPlan)) && !$this->isEditable()) {
            throw new UnivapayLogicError(Reason::PLAN_ALREADY_SET());
        }

        $payload = [
            'transaction_token_id' => $transactionTokenId,
            'initial_amount' => isset($initialAmount) ? $initialAmount->getAmount() : null,
            'period' => isset($period) ? $period->getValue() : null,
            'cyclical_period' => isset($cyclicalPeriod) ? DateUtils::asPeriodString($cyclicalPeriod) : null,
            'schedule_settings' => $scheduleSettings,
            'status' => isset($status) ? $status->getValue() : null,
            'metadata' => $metadata,
            'subscription_plan' => $subscriptionPlan,
            'installment_plan' => $installmentPlan
        ];
        if (isset($money)) {
            $payload += $money->jsonSerialize();
        }
        return $this->update(FunctionalUtils::stripNulls($payload));
    }

    public function cancel()
    {
        if ($this->isTerminal()) {
            throw new UnivapayLogicError(Reason::SUBSCRIPTION_ALREADY_ENDED());
        }
        return RequesterUtils::executeDelete($this->getIdContext());
    }

    public function isEditable()
    {
        switch ($this->status) {
            case SubscriptionStatus::UNVERIFIED():
            case SubscriptionStatus::UNCONFIRMED():
                return true;
            default:
                return false;
        }
    }
    
    public function isProcessing()
    {
        switch ($this->status) {
            case SubscriptionStatus::UNPAID():
            case SubscriptionStatus::CURRENT():
            case SubscriptionStatus::SUSPENDED():
                return true;
            default:
                return false;
        }
    }

    public function isTokenPatchable()
    {
        switch ($this->status) {
            case SubscriptionStatus::UNCONFIRMED():
            case SubscriptionStatus::UNPAID():
            case SubscriptionStatus::CURRENT():
            case SubscriptionStatus::SUSPENDED():
                return true;
            default:
                return false;
        }
    }

    public function isTerminal()
    {
        switch ($this->status) {
            case SubscriptionStatus::CANCELED():
            case SubscriptionStatus::COMPLETED():
                return true;
            default:
                return false;
        }
    }

    public static function isSubscribable(PaymentType $paymentType)
    {
        return PaymentType::CARD() === $paymentType ||
            PaymentType::KONBINI() === $paymentType ||
            PaymentType::APPLE_PAY() === $paymentType;
    }

    protected function getIdContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'subscriptions', $this->id]);
    }

    protected function pollableStatuses()
    {
        return [
            (string) SubscriptionStatus::UNVERIFIED() => array_diff(
                SubscriptionStatus::findValues(),
                [SubscriptionStatus::UNVERIFIED()]
            ),
            (string) SubscriptionStatus::AUTHORIZED() => array_diff(
                SubscriptionStatus::findValues(),
                [SubscriptionStatus::UNVERIFIED(), SubscriptionStatus::AUTHORIZED()]
            )
        ];
    }

    protected function statusPropertyPath()
    {
        return 'status';
    }

    protected function getChargeContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'subscriptions', $this->id, 'charges']);
    }

    protected function getScheduledPaymentContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'subscriptions', $this->id, 'payments']);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('currency', true, FormatterUtils::of('getCurrency'))
            ->upsert('amount', true, FormatterUtils::getMoney('currency'))
            ->upsert('period', false, FormatterUtils::getTypedEnum(Period::class))
            ->upsert('cyclical_period', false, FormatterUtils::of('getDateInterval'))
            ->upsert('initial_amount', false, FormatterUtils::getMoney('currency'))
            ->upsert('schedule_settings', true, ScheduleSettings::getSchema()->getParser())
            ->upsert('amount_left', false, FormatterUtils::getMoney('currency'))
            ->upsert('status', true, FormatterUtils::getTypedEnum(SubscriptionStatus::class))
            ->upsert('mode', true, FormatterUtils::getTypedEnum(AppTokenMode::class))
            ->upsert('created_on', true, FormatterUtils::of('getDateTime'))
            ->upsert('next_payment', false, ScheduledPayment::getSchema()->getParser())
            ->upsert('subscription_plan', false, SubscriptionPlan::getSchema()->getParser())
            ->upsert('installment_plan', false, InstallmentPlan::getSchema()->getParser())
            ->upsert('first_charge_capture_after', false, FormatterUtils::of('getDateInterval'));
    }
}
