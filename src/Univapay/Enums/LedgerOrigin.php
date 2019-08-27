<?php

namespace Univapay\Enums;

final class LedgerOrigin extends TypedEnum
{
    // phpcs:disable
    public static function CANCEL() { return self::create(); }
    public static function CHARGE() { return self::create(); }
    public static function REFUND() { return self::create(); }
    public static function MANUAL() { return self::create(); }
}
