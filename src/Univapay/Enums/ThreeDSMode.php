<?php

namespace Univapay\Enums;

final class ThreeDSMode extends TypedEnum
{
    // phpcs:disable
    public static function FORCE() { return self::create(); }
    public static function IF_AVAILABLE() { return self::create(); }
    public static function NORMAL() { return self::create(); }
    public static function PROVIDED() { return self::create(); }
    public static function REQUIRE() { return self::create(); }
    public static function SKIP() { return self::create(); }
}
