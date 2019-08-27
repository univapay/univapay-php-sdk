<?php

namespace Univapay\Enums;

final class CardBrand extends TypedEnum
{
    // phpcs:disable
    public static function VISA() { return self::create(); }
    public static function AMERICAN_EXPRESS() { return self::create(); }
    public static function MASTERCARD() { return self::create(); }
    public static function MAESTRO() { return self::create(); }
    public static function DISCOVER() { return self::create(); }
    public static function JCB() { return self::create(); }
    public static function DINERS_CLUB() { return self::create(); }
    public static function UNIONPAY() { return self::create(); }
    public static function UNKNOWN() { return self::create(); }
}
