<?php

namespace Univapay\Enums;

final class ChargeType extends TypedEnum
{
    // phpcs:disable
    public static function NORMAL() { return self::create(); }
    public static function CVV_AUTH() { return self::create(); }
}
