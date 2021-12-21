<?php

namespace Univapay\Enums;

final class QrBrand extends TypedEnum
{
    // phpcs:disable
    public static function QQ() { return self::create(); }
    public static function WE_CHAT() { return self::create(); }
    public static function ALIPAY_CHINA() { return self::create(); }
    public static function ALIPAY_HK() { return self::create(); }
    public static function ALIPAY_SINGAPORE() { return self::create(); }
    public static function TOUCH_N_GO() { return self::create('tng'); }
    public static function EZLINK() { return self::create(); }
    public static function GCASH() { return self::create(); }
    public static function DANA() { return self::create(); }
    public static function TRUEMONEY() { return self::create(); }
    public static function BARTONG() { return self::create(); }
    public static function KAKAOPAY() { return self::create(); }
    public static function JKOPAY() { return self::create(); }
    public static function DASH() { return self::create(); }
    public static function GLOBAL_PAY() { return self::create(); }
    public static function ORIGAMI() { return self::create(); }
    public static function D_BARAI() { return self::create(); }
    public static function AU_PAY() { return self::create(); }
    public static function PAY_PAY() { return self::create(); }
    public static function MERPAY() { return self::create(); }
    public static function RAKUTEN_PAY() { return self::create(); }
    public static function LINE_PAY() { return self::create(); }
    public static function GINKO_PAY() { return self::create(); }
    public static function YUCHO_PAY() { return self::create(); }
    public static function HAMA_PAY() { return self::create(); }
    public static function OKI_PAY() { return self::create(); }
    public static function YOKA_PAY_FUKUOKA() { return self::create(); }
    public static function YOKA_PAY_KUMAMOTO() { return self::create(); }
    public static function YOKA_PAY_SHINWA() { return self::create(); }
    public static function HOKUHOKU_PAY_HOKKAIDO() { return self::create(); }
    public static function HOKUHOKU_PAY_HOKURIKU() { return self::create(); }
    public static function COI_PAY_HIROSHIMA() { return self::create(); }
    public static function SMBC() { return self::create(); }
    public static function UNKNOWN() { return self::create(); }
}
