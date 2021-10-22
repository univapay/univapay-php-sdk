<?php

namespace Univapay\Enums;

final class Gateway extends TypedEnum
{
    // phpcs:disable
    public static function ALLIED_WALLET_NEXT_GEN() { return self::create(); }
    public static function BALTIC_BILL() { return self::create(); }
    public static function BLU_SKY() { return self::create(); }
    public static function CYREX_PAY() { return self::create(); }
    public static function FIRST_DATA() { return self::create(); }
    public static function IPS() { return self::create(); }
    public static function MEIKO_PAY() { return self::create(); }
    public static function NCCC() { return self::create(); }
    public static function PAYVISION() { return self::create(); }
    public static function Q_PAY() { return self::create(); }
    public static function SMART_CHECKOUT() { return self::create(); }
    public static function STRATUS() { return self::create(); }
    public static function WIRECARD() { return self::create(); }
    public static function WORLDPAY() { return self::create(); }

    public static function ALIPAY() { return self::create(); }
    public static function ALIPAY_CONNECT() { return self::create(); }
    public static function AU_PAY() { return self::create(); }
    public static function BARTONG() { return self::create(); }
    public static function D_BARAI() { return self::create(); }
    public static function GINKO_PAY() { return self::create(); }
    public static function JKOPAY() { return self::create(); }
    public static function LINE_PAY() { return self::create(); }
    public static function MERPAY() { return self::create(); }
    public static function ORIGAMI() { return self::create(); }
    public static function PAY_PAY() { return self::create(); }
    public static function QQ() { return self::create(); }
    public static function RAKUTEN_PAY() { return self::create(); }
    public static function VIA() { return self::create(); }
    public static function WE_CHAT() { return self::create(); }
    
    public static function ALIPAY_MERCHANT_QR() { return self::create(); }
    public static function ALIPAY_CONNECT_MPM() { return self::create(); }
    public static function PAY_PAY_MERCHANT() { return self::create(); }
    public static function WE_CHAT_MPM() { return self::create(); }

    public static function DENSAN() { return self::create(); }

    public static function PAIDY() { return self::create(); }

    public static function ALIPAY_ONLINE() { return self::create(); }
    public static function ALIPAY_CONNECT_ONLINE() { return self::create(); }
    public static function PAY_PAY_ONLINE() { return self::create(); }
    public static function WE_CHAT_ONLINE() { return self::create(); }
}
