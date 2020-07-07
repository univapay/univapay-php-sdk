<?php

namespace Univapay\Enums;

final class CvvAuthorizationStatus extends TypedEnum
{
    // phpcs:disable
    public static function PENDING() { return self::create(); }
    public static function CURRENT() { return self::create(); }
    public static function FAILED() { return self::create(); }
    public static function INACTIVE() { return self::create(); }
}
