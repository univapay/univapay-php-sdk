<?php

namespace Univapay\Enums;

final class RecurringTokenPrivilege extends TypedEnum
{
    // phpcs:disable
    public static function NONE() { return self::create(); }
    public static function BOUNDED() { return self::create(); }
    public static function INFINITE() { return self::create(); }
}
