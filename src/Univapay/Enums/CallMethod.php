<?php

namespace Univapay\Enums;

final class CallMethod extends TypedEnum
{
    // phpcs:disable
    public static function HTTP_GET() { return self::create(); }
    public static function HTTP_POST() { return self::create(); }
    public static function SDK() { return self::create(); }
}
