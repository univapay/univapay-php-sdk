<?php

namespace Univapay\Enums;

final class ActiveFilter extends TypedEnum
{
    // phpcs:disable
    public static function ACTIVE() { return self::create(); }
    public static function INACTIVE() { return self::create(); }
    public static function ALL() { return self::create(); }
}
