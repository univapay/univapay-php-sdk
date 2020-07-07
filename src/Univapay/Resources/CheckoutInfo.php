<?php

namespace Univapay\Resources;

use Univapay\Enums\AppTokenMode;
use Univapay\Enums\RecurringTokenPrivilege;
use Univapay\Resources\Configuration\CardConfiguration;
use Univapay\Resources\Configuration\QrConfiguration;
use Univapay\Resources\Configuration\ConvenienceConfiguration;
use Univapay\Resources\Configuration\PaidyConfiguration;
use Univapay\Resources\Configuration\ThemeConfiguration;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\Json\JsonSchema;

class CheckoutInfo
{
    use Jsonable;
    public $mode;
    public $recurringTokenPrivilege;
    public $name;
    public $cardConfiguration;
    public $qrScanConfiguration;
    public $convenienceConfiguration;
    public $paidyConfiguration;
    public $paidyPublicKey;
    public $logoImage;
    public $theme;

    public function __construct(
        AppTokenMode $mode,
        RecurringTokenPrivilege $recurringTokenPrivilege,
        $name,
        CardConfiguration $cardConfiguration,
        QrConfiguration $qrScanConfiguration,
        ConvenienceConfiguration $convenienceConfiguration,
        PaidyConfiguration $paidyConfiguration,
        $paidyPublicKey,
        $logoImage,
        ThemeConfiguration $theme
    ) {
        $this->mode = $mode;
        $this->recurringTokenPrivilege = $recurringTokenPrivilege;
        $this->name = $name;
        $this->cardConfiguration = $cardConfiguration;
        $this->qrScanConfiguration = $qrScanConfiguration;
        $this->convenienceConfiguration = $convenienceConfiguration;
        $this->paidyConfiguration = $paidyConfiguration;
        $this->paidyPublicKey = $paidyPublicKey;
        $this->logoImage = $logoImage;
        $this->theme = $theme;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('mode', true, FormatterUtils::getTypedEnum(AppTokenMode::class))
            ->upsert('recurring_token_privilege', true, FormatterUtils::getTypedEnum(RecurringTokenPrivilege::class))
            ->upsert('card_configuration', true, CardConfiguration::getSchema()->getParser())
            ->upsert('qr_scan_configuration', true, QrConfiguration::getSchema()->getParser())
            ->upsert('convenience_configuration', true, ConvenienceConfiguration::getSchema()->getParser())
            ->upsert('paidy_configuration', true, PaidyConfiguration::getSchema()->getParser())
            ->upsert('theme', true, ThemeConfiguration::getSchema()->getParser());
    }
}
