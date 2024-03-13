<?php

namespace Univapay\Enums;

final class ConvenienceStore extends TypedEnum
{
    // phpcs:disable
    public static function SEVEN_ELEVEN() { return self::create(); }
    public static function FAMILY_MART() { return self::create(); }
    public static function LAWSON() { return self::create(); }
    public static function MINI_STOP() { return self::create(); }
    public static function SEICO_MART() { return self::create(); }
    public static function DAILY_YAMAZAKI() { return self::create(); }
    public static function YAMAZAKI_DAILY_STORE() { return self::create(); }
    public static function PAY_EASY() { return self::create(); }
}
