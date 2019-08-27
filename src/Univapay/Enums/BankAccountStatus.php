<?php

namespace Univapay\Enums;

final class BankAccountStatus extends TypedEnum
{
    // phpcs:disable
    public static function NEW_ACCOUNT() { return self::create('new'); }
    public static function UNABLE_TO_VERIFY() { return self::create(); }
    public static function VERIFIED() { return self::create(); }
    public static function ERRORED() { return self::create(); }
}
