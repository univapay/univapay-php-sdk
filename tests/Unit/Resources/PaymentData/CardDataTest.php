<?php
namespace UnivapayTest\Unit\Resources\PaymentData;

use PHPUnit\Framework\TestCase;
use Univapay\Enums\CardBrand;
use Univapay\Enums\CardCategory;
use Univapay\Enums\CardSubBrand;
use Univapay\Enums\CardType;
use Univapay\Enums\ThreeDSStatus;
use Univapay\Resources\PaymentData\CardData;
use UnivapayTest\Integration\IntegrationSuite;

class CardDataTest extends TestCase
{
    use IntegrationSuite;
    
    public function testCardData()
    {
        $str = <<<EOD
        {
            "card": {
                "cardholder": "UNIVAPAY TEST",
                "exp_month": 12,
                "exp_year": 2050,
                "last_four": "1831",
                "brand": "mastercard",
                "country": "JP",
                "card_type": "credit",
                "category": "business",
                "issuer": "xxxxxxxxxxxxxxxx",
                "sub_brand": "visa_electron"
            },
            "billing": {
                "line1": "Line 1 lorem ipsum",
                "line2": "Line 2 lorem ipsum",
                "state": "Tokyo",
                "city": "Tokyo",
                "country": "JP",
                "zip": "xxxxxx",
                "phone_number": {
                    "country_code": 81,
                    "local_number": "0312345678"
                }
            },
            "cvv_authorize": {
                "enabled": false,
                "status": null,
                "charge_id": null,
                "credentials_id": null,
                "currency": null
            },
            "cvv_authorize_check": {
                "status": null,
                "charge_id": null,
                "date": null
            },
            "three_ds": {
                "enabled": true,
                "redirect_endpoint": "https://ec-site.example.com/3ds/complete",
                "status": "pending",
                "redirect_id": "11efbdb4-6820-12dc-8246-6f01ed1243a9",
                "error": null
            }
        }
EOD;
        $json = json_decode($str, true);
        $cardData = CardData::getSchema()->parse($json, [$this->getClient()->getStoreBasedContext()]);

        $this->assertEquals('UNIVAPAY TEST', $cardData->card->cardholder);
        $this->assertEquals(12, $cardData->card->expMonth);
        $this->assertEquals(2050, $cardData->card->expYear);
        $this->assertEquals('1831', $cardData->card->lastFour);
        $this->assertEquals(CardBrand::MASTERCARD(), $cardData->card->brand);
        $this->assertEquals(CardType::CREDIT(), $cardData->card->cardType);
        $this->assertEquals('JP', $cardData->card->country);
        $this->assertEquals(CardCategory::BUSINESS(), $cardData->card->category);
        $this->assertEquals('xxxxxxxxxxxxxxxx', $cardData->card->issuer);
        $this->assertEquals(CardSubBrand::VISA_ELECTRON(), $cardData->card->subBrand);

        $this->assertEquals('Line 1 lorem ipsum', $cardData->billing->line1);
        $this->assertEquals('Line 2 lorem ipsum', $cardData->billing->line2);
        $this->assertEquals('Tokyo', $cardData->billing->state);
        $this->assertEquals('Tokyo', $cardData->billing->city);
        $this->assertEquals('JP', $cardData->billing->country);
        $this->assertEquals('xxxxxx', $cardData->billing->zip);
        $this->assertEquals(81, $cardData->billing->phoneNumber->countryCode);
        $this->assertEquals('0312345678', $cardData->billing->phoneNumber->localNumber);

        $this->assertEquals(false, $cardData->cvvAuthorize->enabled);
        $this->assertEquals(null, $cardData->cvvAuthorize->status);
        $this->assertEquals(null, $cardData->cvvAuthorize->chargeId);
        $this->assertEquals(null, $cardData->cvvAuthorize->credentialsId);
        $this->assertEquals(null, $cardData->cvvAuthorize->currency);

        $this->assertEquals(true, $cardData->threeDS->enabled);
        $this->assertEquals("https://ec-site.example.com/3ds/complete", $cardData->threeDS->redirectEndpoint);
        $this->assertEquals(ThreeDSStatus::PENDING(), $cardData->threeDS->status);
        $this->assertEquals("11efbdb4-6820-12dc-8246-6f01ed1243a9", $cardData->threeDS->redirectId);
        $this->assertEquals(null, $cardData->threeDS->error);
    }
}
