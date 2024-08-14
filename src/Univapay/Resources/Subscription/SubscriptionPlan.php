<?php

namespace Univapay\Resources\Subscription;

use InvalidArgumentException;
use JsonSerializable;
use Univapay\Enums\Field;
use Univapay\Enums\SubscriptionPlanType;
use Univapay\Enums\Reason;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Resources\Jsonable;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;
use Money\Currency;
use Money\Money;

class SubscriptionPlan implements JsonSerializable
{
    use Jsonable;

    public $planType;
    public $fixedCycles;
    public $fixedCycleAmount;

    public function __construct(SubscriptionPlanType $planType, $fixedCycles = null, Money $fixedCycleAmount = null)
    {
        switch ($planType) {
            case SubscriptionPlanType::NONE():
                if ($fixedCycles != null || $fixedCycleAmount != null) {
                    throw new InvalidArgumentException(
                        'None plan do not accept $fixedCycles or $fixedCycleAmount'
                    );
                }
                break;
            case SubscriptionPlanType::FIXED_CYCLES():
                if ($fixedCycles == null || $fixedCycleAmount != null) {
                    throw new InvalidArgumentException(
                        'Fixed cycle plans requires $fixedCycles and not $fixedCycleAmount'
                    );
                }
                break;
            case SubscriptionPlanType::FIXED_CYCLE_AMOUNT():
                if ($fixedCycles != null || $fixedCycleAmount == null) {
                    throw new InvalidArgumentException(
                        'Fixed cycle amount plans requires $fixedCycleAmount and not $fixedCycles'
                    );
                }
        }
        if (isset($fixedCycles) && $fixedCycles < 2) {
            throw new UnivapayValidationError(Field::FIXED_CYCLES(), Reason::NEED_AT_LEAST_TWO_CYCLES());
        }
        if (isset($fixedCycleAmount) && !$fixedCycleAmount->isPositive()) {
            throw new UnivapayValidationError(Field::FIXED_CYCLE_AMOUNT(), Reason::INVALID_FORMAT());
        }

        $this->planType = $planType;
        $this->fixedCycles = $fixedCycles;
        $this->fixedCycleAmount = $fixedCycleAmount;
    }

    public function jsonSerialize() : array
    {
        $data = ['plan_type' => $this->planType->getValue()];
        switch ($this->planType) {
            case SubscriptionPlanType::FIXED_CYCLES():
                $data[$this->planType->getValue()] = $this->fixedCycles;
                break;
            case SubscriptionPlanType::FIXED_CYCLE_AMOUNT():
                $data[$this->planType->getValue()] = $this->fixedCycleAmount->getAmount();
                break;
        }
        return $data;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('plan_type', true, FormatterUtils::getTypedEnum(SubscriptionPlanType::class))
            ->upsert('fixed_cycle_amount', false, FormatterUtils::getMoney('currency', true));
    }
}
