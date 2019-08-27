<?php

namespace Univapay\Enums;

final class TokenType extends TypedEnum
{
    // phpcs:disable
    public static function ONE_TIME() { return self::create(); }
    public static function RECURRING() { return self::create(); }
    public static function SUBSCRIPTION() { return self::create(); }
}
