<?php

namespace Univapay\Enums;

final class QRBrand extends TypedEnum
{
    // phpcs:disable
    public static function QQ() { return self::create(); }
    public static function ALIPAY_CHINA() { return self::create(); }
    public static function ALIPAY_HK() { return self::create(); }
    public static function ALIPAY_SINGAPORE() { return self::create(); }
    public static function WE_CHAT() { return self::create(); }
    public static function ORIGAMI() { return self::create(); }
    public static function D_BARAI() { return self::create(); }
    public static function PAY_PAY() { return self::create(); }
    public static function MERPAY() { return self::create(); }
    public static function KAKAOPAY() { return self::create(); }
    public static function BARTONG() { return self::create(); }
    public static function RAKUTEN_PAY() { return self::create(); }
    public static function JKOPAY() { return self::create(); }
    public static function TOUCH_N_GO() { return self::create('tng'); }
    public static function EZLINK() { return self::create('tng'); }
    public static function GCASH() { return self::create('tng'); }
    public static function DANA() { return self::create('tng'); }
    public static function TRUEMONEY() { return self::create('tng'); }
    public static function DASH() { return self::create(); }
    public static function GLOBAL_PAY() { return self::create(); }
}
