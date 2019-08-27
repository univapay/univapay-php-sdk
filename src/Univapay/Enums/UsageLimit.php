<?php

namespace Univapay\Enums;

final class UsageLimit extends TypedEnum
{
    // phpcs:disable
    public static function DAILY() { return self::create(); }
    public static function WEEKLY() { return self::create(); }
    public static function MONTHLY() { return self::create(); }
    public static function ANNUALLY() { return self::create(); }
}
