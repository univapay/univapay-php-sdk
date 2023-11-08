<?php

namespace Univapay\Enums;

final class QrBrandMerchant extends TypedEnum
{
    // phpcs:disable
    public static function ALIPAY_MERCHANT_QR() { return self::create(); }
    public static function ALIPAY_CONNECT_MPM() { return self::create(); }
    public static function D_BARAI_MERCHANT() { return self::create(); }
    public static function PAY_PAY_MERCHANT() { return self::create(); }
    public static function WE_CHAT_MPM() { return self::create(); }

    public static function ALIPAY_CHINA() { return self::create(); }
    public static function ALIPAY_HK() { return self::create(); }
    public static function ALIPAY_SINGAPORE() { return self::create(); }
    public static function KAKAOPAY() { return self::create(); }
    public static function TOUCH_N_GO() { return self::create('tng'); }
    public static function EZLINK() { return self::create(); }
    public static function GCASH() { return self::create(); }
    public static function DANA() { return self::create(); }
    public static function TRUEMONEY() { return self::create(); }
    public static function TINBA() { return self::create(); }
    public static function NAVERPAY() { return self::create(); }
    public static function TOSS() { return self::create(); }
    public static function OCBC() { return self::create(); }
    public static function CHANGIPAY() { return self::create(); }
    public static function HIPAY() { return self::create(); }
    public static function PUBLICBANK() { return self::create('pbengagemy'); }
    public static function MPAY() { return self::create(); }
}
