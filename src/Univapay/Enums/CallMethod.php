<?php

namespace Univapay\Enums;

final class CallMethod extends TypedEnum
{
    // phpcs:disable
    public static function HTTP_GET() { return self::create(); }
    public static function HTTP_GET_MOBILE() { return self::create(); }
    public static function HTTP_POST() { return self::create(); }
    public static function SDK() { return self::create(); }
    public static function WEB() { return self::create(); }
    public static function APP() { return self::create(); }
}
