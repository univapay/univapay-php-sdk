<?php

namespace Univapay\Enums;

final class AppTokenMode extends TypedEnum
{
    // phpcs:disable
    public static function TEST() { return self::create(); }
    public static function LIVE() { return self::create(); }
}
