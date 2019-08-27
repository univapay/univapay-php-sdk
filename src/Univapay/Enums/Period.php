<?php

namespace Univapay\Enums;

final class Period extends TypedEnum
{
    // phpcs:disable
    public static function DAILY() { return self::create(); }
    public static function WEEKLY() { return self::create(); }
    public static function BIWEEKLY() { return self::create(); }
    public static function MONTHLY() { return self::create(); }
    public static function QUARTERLY() { return self::create(); }
    public static function SEMIANNUALLY() { return self::create(); }
    public static function ANNUALLY() { return self::create(); }
}
