<?php

namespace Univapay\Enums;

final class BankAccountType extends TypedEnum
{
    // phpcs:disable
    public static function CHECKING() { return self::create(); }
    public static function SAVINGS() { return self::create(); }
}
