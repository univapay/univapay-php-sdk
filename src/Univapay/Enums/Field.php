<?php

namespace Univapay\Enums;

final class Field extends TypedEnum
{
    // phpcs:disable
    public static function AMOUNT() { return self::create(); }
    public static function CAPTURE_AT() { return self::create(); }
    public static function COUNTRY_CODE() { return self::create(); }
    public static function EXPIRATION_PERIOD() { return self::create(); }
    public static function FIXED_CYCLES() { return self::create(); }
    public static function FIXED_CYCLE_AMOUNT() { return self::create(); }
    public static function INITIAL_AMOUNT() { return self::create(); }
    public static function LOCAL_NUMBER() { return self::create(); }
    public static function PERIOD() { return self::create(''); }
    public static function PHONE_NUMBER() { return self::create(''); }
    public static function PRESERVE_END_OF_MONTH() { return self::create('schedule_settings.preserve_end_of_month'); }
    public static function REASON() { return self::create(); }
    public static function START_ON() { return self::create(); }
    public static function STATUS() { return self::create(); }
    public static function TYPE() { return self::create(); }
    public static function ZIP() { return self::create(''); }
}
