<?php

namespace Univapay\Enums;

final class QrBrandMerchant extends TypedEnum
{
    // phpcs:disable
    public static function ALIPAY_CONNECT_MPM() { return self::create(); }
    public static function ALIPAY_CHINA() { return self::create(); }
    public static function ALIPAY_HK() { return self::create(); }
    public static function ALIPAY_SINGAPORE() { return self::create(); }
    public static function KAKAOPAY() { return self::create(); }
    public static function ALIPAY_MERCHANT_QR() { return self::create(); }
    public static function PAY_PAY_MERCHANT() { return self::create(); }
}
