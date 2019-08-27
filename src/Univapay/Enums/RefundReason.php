<?php

namespace Univapay\Enums;

final class RefundReason extends TypedEnum
{
    // phpcs:disable
    public static function DUPLICATE() { return self::create(); }
    public static function FRAUD() { return self::create(); }
    public static function CUSTOMER_REQUEST() { return self::create(); }
    public static function CHARGEBACK() { return self::create(); }
}
