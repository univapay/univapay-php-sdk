<?php

namespace Univapay\Enums;

final class QrBrandMerchant extends TypedEnum
{
    // phpcs:disable
    public static function ALIPAY_MERCHANT_QR() { return self::create(); }
    public static function PAY_PAY_MERCHANT() { return self::create(); }
}
