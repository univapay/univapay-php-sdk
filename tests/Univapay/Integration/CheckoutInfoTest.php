<?php
namespace UnivapayTest\Integration;

use DateTime;
use Univapay\Enums\AppTokenMode;
use Univapay\Enums\RecurringTokenPrivilege;
use Univapay\Resources\CheckoutInfo;
use PHPUnit\Framework\TestCase;

class CheckoutInfoTest extends TestCase
{
    use IntegrationSuite;

    public function testCheckoutInfoParse()
    {
        $str = <<<EOD
        {
            "mode": "test",
            "recurring_token_privilege": "infinite",
            "name": "My Store",
            "card_configuration": {
                "enabled": true,
                "debit_enabled": true,
                "prepaid_enabled": true,
                "forbidden_card_brands": null,
                "allowed_countries_by_ip": null,
                "foreign_cards_allowed": null,
                "fail_on_new_email": null,
                "card_limit": null,
                "allow_empty_cvv": null
            },
            "qr_scan_configuration": {
                "enabled": false,
                "forbidden_qr_scan_gateways": null
            },
            "convenience_configuration": {
                "enabled": true
            },
            "paidy_configuration": {
                "enabled": true
            },
            "paidy_public_key": "pk_test_1234567890abcdefghijklmnop",
            "logo_image": "https://someImage.com/abc.png",
            "theme": {
                "colors": {
                    "main_background": "#fafafa",
                    "secondary_background": "#ee7a00",
                    "main_color": "#fafafa",
                    "main_text": "#838383",
                    "primary_text": "#fafafa",
                    "secondary_text": "#222222",
                    "base_text": "#000000"
                }
            }
        }
EOD;

        $json = json_decode($str, true);
        $checkoutInfo = CheckoutInfo::getSchema()->parse($json, [$this->getClient()->getStoreBasedContext()]);
        $this->assertEquals(AppTokenMode::TEST(), $checkoutInfo->mode);
        $this->assertEquals(RecurringTokenPrivilege::INFINITE(), $checkoutInfo->recurringTokenPrivilege);
        $this->assertEquals('My Store', $checkoutInfo->name);
        $this->assertTrue($checkoutInfo->cardConfiguration->enabled);
        $this->assertTrue($checkoutInfo->cardConfiguration->debitEnabled);
        $this->assertTrue($checkoutInfo->cardConfiguration->prepaidEnabled);
        $this->assertNull($checkoutInfo->cardConfiguration->forbiddenCardBrands);
        $this->assertNull($checkoutInfo->cardConfiguration->allowedCountriesByIp);
        $this->assertNull($checkoutInfo->cardConfiguration->foreignCardsAllowed);
        $this->assertNull($checkoutInfo->cardConfiguration->failOnNewEmail);
        $this->assertNull($checkoutInfo->cardConfiguration->cardLimit);
        $this->assertNull($checkoutInfo->cardConfiguration->allowEmptyCvv);
        $this->assertFalse($checkoutInfo->qrScanConfiguration->enabled);
        $this->assertNull($checkoutInfo->qrScanConfiguration->forbiddenQrScanGateway);
        $this->assertTrue($checkoutInfo->convenienceConfiguration->enabled);
        $this->assertTrue($checkoutInfo->paidyConfiguration->enabled);
        $this->assertEquals('pk_test_1234567890abcdefghijklmnop', $checkoutInfo->paidyPublicKey);
        $this->assertEquals('https://someImage.com/abc.png', $checkoutInfo->logoImage);
        $this->assertEquals('#fafafa', $checkoutInfo->theme->colors->mainBackground);
        $this->assertEquals('#ee7a00', $checkoutInfo->theme->colors->secondaryBackground);
        $this->assertEquals('#fafafa', $checkoutInfo->theme->colors->mainColor);
        $this->assertEquals('#838383', $checkoutInfo->theme->colors->mainText);
        $this->assertEquals('#fafafa', $checkoutInfo->theme->colors->primaryText);
        $this->assertEquals('#222222', $checkoutInfo->theme->colors->secondaryText);
        $this->assertEquals('#000000', $checkoutInfo->theme->colors->baseText);

        $checkoutInfoLive = $this->getClient()->getCheckoutInfo();
        $this->assertTrue(is_string($checkoutInfoLive->name));
    }

    public function testGetCheckoutInfoWithoutSecret()
    {
        $checkoutInfo = $this->getClient()->getCheckoutInfo();
        $this->assertEquals(AppTokenMode::TEST(), $checkoutInfo->mode);
    }
}
