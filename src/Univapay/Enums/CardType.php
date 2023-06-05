<?php

namespace Univapay\Enums;

final class CardType extends TypedEnum
{
    // phpcs:disable
    public static function CREDIT() { return self::create(); }
    public static function DEBIT() { return self::create(); }
    public static function CHARGE_CARD() { return self::create(); }
    public static function UNKNOWN() { return self::create(); }
}
