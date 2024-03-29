<?php

namespace Univapay\Enums;

final class WebhookEvent extends TypedEnum
{
    // phpcs:disable
    public static function TOKEN_CREATED() { return self::create(); }
    public static function TOKEN_UPDATED() { return self::create(); }
    public static function TOKEN_CVV_AUTH_UPDATED() { return self::create(); }
    public static function RECURRING_TOKEN_DELETED() { return self::create(); }
    
    public static function CHARGE_UPDATED() { return self::create(); }
    public static function CHARGE_FINISHED() { return self::create(); }

    public static function SUBSCRIPTION_PAYMENT() { return self::create(); }
    public static function SUBSCRIPTION_COMPLETED() { return self::create(); }
    public static function SUBSCRIPTION_FAILURE() { return self::create(); }
    public static function SUBSCRIPTION_CANCELED() { return self::create(); }
    public static function SUBSCRIPTION_SUSPENDED() { return self::create(); }

    public static function REFUND_FINISHED() { return self::create(); }

    public static function CANCEL_FINISHED() { return self::create(); }

    public static function CUSTOMS_DECLARATION_FINISHED() { return self::create(); }

    public static function TRANSFER_CREATED() { return self::create(); }
    public static function TRANSFER_UPDATED() { return self::create(); }
    public static function TRANSFER_FINALIZED() { return self::create(); }
    
}
