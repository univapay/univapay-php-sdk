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
    public static function KAKAOPAY() { return self::create(); }
}
