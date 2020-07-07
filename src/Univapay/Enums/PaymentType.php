<?php

namespace Univapay\Enums;

final class PaymentType extends TypedEnum
{
    // phpcs:disable
    public static function CARD() { return self::create(); }
    public static function QR_SCAN() { return self::create(); }
    public static function QR_MERCHANT() { return self::create(); }
    public static function KONBINI() { return self::create(); }
    public static function APPLE_PAY() { return self::create(); }
    public static function PAIDY() { return self::create(); }
    public static function ONLINE() { return self::create(); }
}
