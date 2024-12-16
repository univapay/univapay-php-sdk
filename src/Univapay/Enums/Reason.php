<?php

namespace Univapay\Enums;

final class Reason extends TypedEnum
{
    // phpcs:disable
    // SDK specific
    public static function REQUIRES_APP_TOKEN() { return self::create('A store or merchant app token is required during client creation'); }
    public static function REQUIRES_STORE_APP_TOKEN() { return self::create('A store app token is required and has not been included during client creation'); }
    public static function REQUIRES_MERCHANT_APP_TOKEN() { return self::create('A merchant app token is required and has not been included during client creation'); }
    public static function UNSUPPORTED_FEATURE() { return self::create('This feature is currently unsupported by the SDK'); }

    // Generic
    public static function REQUIRED_VALUE() { return self::create(); }
    public static function INVALID_AMOUNT() { return self::create(); }
    public static function INVALID_FORMAT() { return self::create(); }
    public static function INVALID_PERMISSIONS() { return self::create(); }
    public static function INVALID_TOKEN_TYPE() { return self::create(); }
    public static function INVALID_SCHEDULED_CAPTURE_DATE() { return self::create(); }
    public static function MUST_BE_FUTURE_TIME() { return self::create(); }
    public static function FORBIDDEN_PARAMETER() { return self::create(); }
    public static function EXPIRATION_DATE_OUT_OF_BOUNDS() { return self::create(); }
    
    // Transaction Tokens
    public static function RECURRING_TOKEN_NOT_ALLOWED() { return self::create(); }
    public static function TRANSACTION_TOKEN_IS_NOT_RECURRING() { return self::create(); }
    public static function INVALID_PHONE_NUMBER() { return self::create(); }
    public static function ONLY_JAPANESE_PHONE_NUMBER_ALLOWED() { return self::create(); }

    // Charges
    public static function CAPTURE_ONLY_FOR_CARD_PAYMENT() { return self::create(); }

    // Subscriptions
    public static function NON_SUBSCRIPTION_PAYMENT() { return self::create(); }
    public static function NOT_SUBSCRIPTION_PAYMENT() { return self::create(); }
    public static function SUBSCRIPTION_ALREADY_ENDED() { return self::create(); }
    public static function SUBSCRIPTION_PROCESSING() { return self::create(); }
    public static function PLAN_ALREADY_SET() { return self::create(); }
    public static function MUST_BE_MONTH_BASE_TO_SET() { return self::create(); }
    public static function CANNOT_CHANGE_CANCELED_SUBSCRIPTION() { return self::create(); }
    public static function CANNOT_SET_AFTER_SUBSCRIPTION_STARTED() { return self::create(); }
    public static function CANNOT_CHANGE_TOKEN() { return self::create(); }
    public static function NEED_AT_LEAST_TWO_CYCLES() { return self::create(); }
    public static function PERIOD_OR_CYCLICAL_PERIOD_MUST_BE_SET() { return self::create(); }

    // ThreeDS MPI
    public static function INVALID_THREE_DS_MPI_FIELDS() { return self::create('All fields must be set when using 3DS MPI'); }
}
