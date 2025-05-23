<?php

namespace Univapay\Enums;

final class ThreeDSStatus extends TypedEnum
{
    // phpcs:disable
    public static function PENDING() { return self::create(); }
    public static function AWAITING() { return self::create(); }
    public static function SUCCESSFUL() { return self::create(); }
    public static function FAILED() { return self::create(); }
    public static function ERROR() { return self::create(); }
}
