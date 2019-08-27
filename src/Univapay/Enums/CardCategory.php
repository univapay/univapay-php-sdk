<?php

namespace Univapay\Enums;

final class CardCategory extends TypedEnum
{
    // phpcs:disable
    public static function CLASSIC() { return self::create(); }
    public static function CORPORATE() { return self::create(); }
    public static function PREPAID() { return self::create(); }
}
