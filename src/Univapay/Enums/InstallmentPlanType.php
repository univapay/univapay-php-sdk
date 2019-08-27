<?php

namespace Univapay\Enums;

final class InstallmentPlanType extends TypedEnum
{
    // phpcs:disable
    public static function NONE() { return self::create('null'); } // Only when deleting an installment plan via patch
    public static function REVOLVING() { return self::create(); }
    public static function FIXED_CYCLES() { return self::create(); }
    public static function FIXED_CYCLE_AMOUNT() { return self::create(); }
}
