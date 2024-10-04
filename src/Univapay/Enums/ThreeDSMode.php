<?php

namespace Univapay\Enums;

final class ThreeDSMode extends TypedEnum
{
    // phpcs:disable
    public static function NORMAL() { return self::create(); }
    public static function REQUIRE() { return self::create(); }
    public static function FORCE() { return self::create(); }
    public static function SKIP() { return self::create(); }
}
