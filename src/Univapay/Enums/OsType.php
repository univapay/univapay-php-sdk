<?php

namespace Univapay\Enums;

final class OsType extends TypedEnum
{
    // phpcs:disable
    public static function IOS() { return self::create(); }
    public static function ANDROID() { return self::create(); }
}
