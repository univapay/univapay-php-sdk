<?php

namespace Univapay\Enums;

final class OnlineBrand extends TypedEnum
{
    // phpcs:disable
    public static function ALIPAY_ONLINE() { return self::create(); }
    public static function ALIPAY_CHINA() { return self::create(); }
    public static function ALIPAY_HK() { return self::create(); }
    public static function TOUCH_N_GO() { return self::create('tng'); }
    public static function B_KASH() { return self::create(); }
    public static function GCASH() { return self::create(); }
    public static function DANA() { return self::create(); }
    public static function TRUEMONEY() { return self::create(); }
    public static function EASYPAISA() { return self::create(); }
    public static function KAKAOPAY() { return self::create(); }
    public static function PAY_PAY_ONLINE() { return self::create(); }
}
