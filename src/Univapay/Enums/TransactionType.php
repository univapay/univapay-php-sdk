<?php

namespace Univapay\Enums;

final class TransactionType extends TypedEnum
{
    // phpcs:disable
    public static function CHARGE() { return self::create(); }
    public static function REFUND() { return self::create(); }
}
