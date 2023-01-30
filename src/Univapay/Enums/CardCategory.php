<?php

namespace Univapay\Enums;

final class CardCategory extends TypedEnum
{
    // phpcs:disable
    public static function ATM() { return self::create(); }
    public static function BLACK() { return self::create(); }
    public static function BUSINESS() { return self::create(); }
    public static function CENTURION() { return self::create(); }
    public static function CHARGE_CARD() { return self::create(); }
    public static function CLASSIC() { return self::create(); }
    public static function CORPORATE() { return self::create(); }
    public static function CREDIT() { return self::create(); }
    public static function DEBIT() { return self::create(); }
    public static function ELECTRON() { return self::create(); }
    public static function GOLD() { return self::create(); }
    public static function MAESTRO() { return self::create(); }
    public static function PERSONAL() { return self::create(); }
    public static function PLATINUM() { return self::create(); }
    public static function PREPAID() { return self::create(); }
    public static function SIGNATURE() { return self::create(); }
    public static function STANDARD() { return self::create(); }
    public static function TITANIUM() { return self::create(); }
    public static function WORLD() { return self::create(); }
}
