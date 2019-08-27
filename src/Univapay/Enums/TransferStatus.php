<?php

namespace Univapay\Enums;

final class TransferStatus extends TypedEnum
{
    // phpcs:disable
    public static function CREATED() { return self::create(); }
    public static function APPROVED() { return self::create(); }
    public static function CANCELED() { return self::create(); }
    public static function PROCESSING() { return self::create(); }
    public static function PAID() { return self::create(); }
    public static function FAILED() { return self::create(); }
    public static function BLANK() { return self::create(); }
}
