<?php

namespace Univapay\Resources;

use Univapay\Enums\AppTokenMode;
use Univapay\Enums\RecurringTokenPrivilege;
use Univapay\Resources\Configuration\CardConfiguration;
use Univapay\Resources\Configuration\QrScanConfiguration;
use Univapay\Resources\Configuration\ConvenienceConfiguration;
use Univapay\Resources\Configuration\OnlineConfiguration;
use Univapay\Resources\Configuration\PaidyConfiguration;
use Univapay\Resources\Configuration\SubscriptionConfiguration;
use Univapay\Resources\Configuration\SupportedBrand;
use Univapay\Resources\Configuration\ThemeConfiguration;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class CheckoutInfo
{
    use Jsonable;
    public $mode;
    public $recurringTokenPrivilege;
    public $name;
    public $subscriptionConfiguration;
    public $cardConfiguration;
    public $qrScanConfiguration;
    public $convenienceConfiguration;
    public $onlineConfiguration;
    public $paidyConfiguration;
    public $paidyPublicKey;
    public $supportedBrands;
    public $logoImage;
    public $theme;

    public function __construct(
        AppTokenMode $mode,
        RecurringTokenPrivilege $recurringTokenPrivilege,
        $name,
        SubscriptionConfiguration $subscriptionConfiguration,
        CardConfiguration $cardConfiguration,
        QrScanConfiguration $qrScanConfiguration,
        ConvenienceConfiguration $convenienceConfiguration,
        OnlineConfiguration $onlineConfiguration,
        PaidyConfiguration $paidyConfiguration,
        $paidyPublicKey,
        array $supportedBrands,
        $logoImage,
        ThemeConfiguration $theme
    ) {
        $this->mode = $mode;
        $this->recurringTokenPrivilege = $recurringTokenPrivilege;
        $this->name = $name;
        $this->subscriptionConfiguration = $subscriptionConfiguration;
        $this->cardConfiguration = $cardConfiguration;
        $this->qrScanConfiguration = $qrScanConfiguration;
        $this->convenienceConfiguration = $convenienceConfiguration;
        $this->onlineConfiguration = $onlineConfiguration;
        $this->paidyConfiguration = $paidyConfiguration;
        $this->paidyPublicKey = $paidyPublicKey;
        $this->supportedBrands = $supportedBrands;
        $this->logoImage = $logoImage;
        $this->theme = $theme;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('mode', true, FormatterUtils::getTypedEnum(AppTokenMode::class))
            ->upsert('recurring_token_privilege', true, FormatterUtils::getTypedEnum(RecurringTokenPrivilege::class))
            ->upsert('subscription_configuration', true, SubscriptionConfiguration::getSchema()->getParser())
            ->upsert('card_configuration', true, CardConfiguration::getSchema()->getParser())
            ->upsert('qr_scan_configuration', true, QrScanConfiguration::getSchema()->getParser())
            ->upsert('convenience_configuration', true, ConvenienceConfiguration::getSchema()->getParser())
            ->upsert('online_configuration', true, OnlineConfiguration::getSchema()->getParser())
            ->upsert('paidy_configuration', true, PaidyConfiguration::getSchema()->getParser())
            ->upsert('supported_brands', true, FormatterUtils::getListOf(SupportedBrand::getSchema()->getParser()))
            ->upsert('theme', true, ThemeConfiguration::getSchema()->getParser());
    }
}
