<?php

namespace Univapay\Enums;

final class SubscriptionStatus extends TypedEnum
{
    // phpcs:disable
    public static function UNVERIFIED() { return self::create(); }
    public static function UNCONFIRMED() { return self::create(); }
    public static function UNPAID() { return self::create(); }
    public static function CURRENT() { return self::create(); }
    public static function SUSPENDED() { return self::create(); }
    public static function CANCELED() { return self::create(); }
    public static function COMPLETED() { return self::create(); }
}
