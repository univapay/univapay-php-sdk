<?php

namespace Univapay\Enums;

final class SubscriptionPlanType extends TypedEnum
{
    // phpcs:disable
    public static function NONE() { return self::create('null'); } // Only when deleting an subscription plan via patch
    public static function FIXED_CYCLES() { return self::create(); }
    public static function FIXED_CYCLE_AMOUNT() { return self::create(); }
}
