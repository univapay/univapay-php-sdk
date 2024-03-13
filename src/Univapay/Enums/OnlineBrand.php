<?php

namespace Univapay\Enums;

final class OnlineBrand extends TypedEnum
{
    // phpcs:disable
    public static function ALIPAY_ONLINE() { return self::create(); }
    public static function ALIPAY_PLUS_ONLINE() { return self::create(); }
    public static function D_BARAI_ONLINE() { return self::create(); }
    public static function PAY_PAY_ONLINE() { return self::create(); }
    public static function WE_CHAT_ONLINE() { return self::create(); }

    public static function ALIPAY_CHINA() { return self::create(); }
    public static function ALIPAY_HK() { return self::create(); }
    public static function B_KASH() { return self::create(); }
    public static function BOOST() { return self::create(); }
    public static function BPI() { return self::create(); }
    public static function DANA() { return self::create(); }
    public static function EASYPAISA() { return self::create(); }
    public static function GCASH() { return self::create(); }
    public static function GRAB_SG() { return self::create(); }
    public static function KAKAOPAY() { return self::create(); }
    public static function KREDIVO_ID() { return self::create(); }
    public static function MAYA() { return self::create(); }
    public static function NAVER_PAY() { return self::create(); }
    public static function RABBIT_LINE_PAY() { return self::create(); }
    public static function TINBA() { return self::create(); }
    public static function TOSS_PAY() { return self::create(); }
    public static function TOUCH_N_GO() { return self::create('tng'); }
    public static function TRUEMONEY() { return self::create(); }
}
