<?php

namespace Univapay\Enums;

final class CardSubBrand extends TypedEnum
{
    // phpcs:disable
    public static function NONE() { return self::create(); }
    public static function VISA_ELECTRON() { return self::create(); }
    public static function DANKORT() { return self::create(); }
    public static function DINERS_CLUB_CARTE_BLANCHE() { return self::create(); }
    public static function DINERS_CLUB_INTERNATIONAL() { return self::create(); }
    public static function DINERS_CLUB_US_CANADA() { return self::create(); }
}
