<?php

namespace Univapay\Enums;

final class ChargeStatus extends TypedEnum
{
    // phpcs:disable
    public static function PENDING() { return self::create(); }
    public static function AUTHORIZED() { return self::create(); }
    public static function SUCCESSFUL() { return self::create(); }
    public static function FAILED() { return self::create(); }
    public static function ERROR() { return self::create(); }
    public static function CANCELED() { return self::create(); }
    public static function AWAITING() { return self::create(); }
}
