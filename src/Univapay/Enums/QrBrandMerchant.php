<?php

namespace Univapay\Enums;

final class QrBrandMerchant extends TypedEnum
{
    // phpcs:disable
    public static function ALIPAY_MERCHANT_QR() { return self::create(); }
    public static function ALIPAY_CONNECT_MPM() { return self::create(); }
    public static function ALIPAY_PLUS_MPM() { return self::create(); }
    public static function D_BARAI_MERCHANT() { return self::create(); }
    public static function PAY_PAY_MERCHANT() { return self::create(); }
    public static function WE_CHAT_MPM() { return self::create(); }

    public static function ALIPAY_CHINA() { return self::create(); }
    public static function ALIPAY_HK() { return self::create(); }
    public static function ALIPAY_SINGAPORE() { return self::create(); }
    public static function BIGPAY_MY() { return self::create(); }
    public static function BIGPAY_SG() { return self::create(); }
    public static function BIGPAY_TH() { return self::create(); }
    public static function KAKAOPAY() { return self::create(); }
    public static function KASPI_KZ() { return self::create(); }
    public static function KPLUS() { return self::create(); }
    public static function TOUCH_N_GO() { return self::create('tng'); }
    public static function EZLINK() { return self::create(); }
    public static function GCASH() { return self::create(); }
    public static function DANA() { return self::create(); }
    public static function TRUEMONEY() { return self::create(); }
    public static function TINBA() { return self::create(); }
    public static function NAVERPAY() { return self::create(); }
    public static function TOSSPAY() { return self::create(); }
    public static function OCBC() { return self::create(); }
    public static function CHANGIPAY() { return self::create(); }
    public static function HIPAY() { return self::create(); }
    public static function PUBLICBANK() { return self::create('pbengagemy'); }
    public static function MPAY() { return self::create(); }
    public static function HELLOMONEY() { return self::create(); }
    public static function TINABA() { return self::create(); }
    public static function PBENGAGEMY() { return self::create(); }
    public static function ZALOPAY() { return self::create(); }
    public static function BLUECODE() { return self::create(); }
    public static function NAYA_PAY() { return self::create(); }
    public static function SCB_PLANET_PLUS() { return self::create(); }
    public static function HUMO() { return self::create(); }
    public static function MOMO() { return self::create(); }
}
