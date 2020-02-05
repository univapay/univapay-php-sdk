<?php
namespace UnivapayTest\Integration;

use Univapay\Resources\Paginated;
use Univapay\Resources\Store;
use PHPUnit\Framework\TestCase;

class StoreTest extends TestCase
{
    use IntegrationSuite;

    /**
     * @group failing
     */
    public function testGetStore()
    {
        $str = <<<EOD
        {
          "id": "11111111-1111-1111-1111-111111111111",
          "name": "Store 1",
          "created_on": "2017-03-21T01:32:13.702689Z",
          "configuration": {
            "percent_fee": null,
            "flat_fees": [],
            "logo_url": "https://example.com/logo.png",
            "country": null,
            "language": null,
            "display_time_zone": null,
            "min_transfer_payout": 15000,
            "maximum_charge_amounts": 500000,
            "transfer_schedule": null,
            "user_transactions_configuration":{
               "enabled": true,
               "notify_customer": true
            },
            "card_configuration": {
              "enabled": true,
              "debit_enabled": null,
              "prepaid_enabled": true,
              "forbidden_card_brands": [
                "maestro",
                "unionpay"
              ],
              "allowed_countries_by_ip": null,
              "foreign_cards_allowed": null,
              "fail_on_new_email": null,
              "card_limit": null,
              "allow_empty_cvv":null
            },
            "qr_scan_configuration": {
              "enabled": true,
              "forbidden_qr_scan_gateways": null
            },
            "convenience_configuration": {
              "enabled": true
            },
            "paidy_configuration": {
              "enabled": true
            },
            "recurring_token_configuration": {
              "recurring_type": "bounded",
              "charge_wait_period": null,
              "card_charge_cvv_confirmation": {
                "enabled": true,
                "threshold": 1500
              }
            },
            "security_configuration": {
              "inspect_suspicious_login_after": "P7D",
              "refund_percent_limit": 80,
              "limit_charge_by_card_configuration": {
                "quantity_of_charges":500,
                "duration_window":"P30D"
              },
              "confirmation_required": true
            },
            "installments_configuration": {
              "enabled": true,
              "min_charge_amount": 1000,
              "max_payout_period": "P50D"
            },
            "card_brand_percent_fees": {
              "visa": 0.05,
              "american_express": null,
              "mastercard": null,
              "maestro": null,
              "discover": null,
              "jcb": null,
              "diners_club": null,
              "union_pay": null
            }
          }
        }
EOD;
        $json = json_decode($str, true);
        $store = Store::getSchema()->parse($json, [$this->getClient()->getStoreBasedContext()]);
        $this->assertEquals('11111111-1111-1111-1111-111111111111', $store->id);
        $this->assertEquals('Store 1', $store->name);
        $this->assertEquals(date_create('2017-03-21T01:32:13.702689Z'), $store->createdOn);
        $this->assertEquals('https://example.com/logo.png', $store->configuration->logoUrl);
        $this->assertEquals(15000, $store->configuration->minTransferPayout);
        $this->assertEquals(500000, $store->configuration->maximumChargeAmounts);
        $this->assertTrue($store->configuration->userTransactionsConfiguration->enabled);
        $this->assertTrue($store->configuration->userTransactionsConfiguration->notifyCustomer);
        $this->assertTrue($store->configuration->cardConfiguration->enabled);
        $this->assertEquals(
            ['maestro', 'unionpay'],
            $store->configuration->cardConfiguration->forbiddenCardBrands
        );
        $this->assertTrue($store->configuration->qrScanConfiguration->enabled);
        $this->assertTrue($store->configuration->convenienceConfiguration->enabled);
        $this->assertTrue($store->configuration->paidyConfiguration->enabled);
        $this->assertEquals('bounded', $store->configuration->recurringTokenConfiguration->recurringType);
        $this->assertTrue($store->configuration->recurringTokenConfiguration->cardChargeCvvConfirmation->enabled);
        $this->assertEquals(
            1500,
            $store->configuration->recurringTokenConfiguration->cardChargeCvvConfirmation->threshold
        );
        $this->assertEquals('bounded', $store->configuration->recurringTokenConfiguration->recurringType);
        $this->assertEquals('P7D', $store->configuration->securityConfiguration->inspectSuspiciousLoginAfter);
        $this->assertEquals(80, $store->configuration->securityConfiguration->refundPercentLimit);
        $this->assertEquals(
            500,
            $store->configuration->securityConfiguration->limitChargeByCardConfiguration->quantityOfCharges
        );
        $this->assertEquals(
            'P30D',
            $store->configuration->securityConfiguration->limitChargeByCardConfiguration->durationWindow
        );
        $this->assertTrue($store->configuration->securityConfiguration->confirmationRequired);
        $this->assertTrue($store->configuration->installmentsConfiguration->enabled);
        $this->assertEquals(1000, $store->configuration->installmentsConfiguration->minChargeAmount);
        $this->assertEquals('P50D', $store->configuration->installmentsConfiguration->maxPayoutPeriod);
        $this->assertEquals(0.05, $store->configuration->cardBrandPercentFees->visa);
    }

    public function testListStores()
    {
        $str = <<<EOD
        {
          "items": [
            {
              "id": "11111111-1111-1111-1111-111111111111",
              "platform_id": "22222222-2222-2222-2222-222222222222",
              "merchant_id": "33333333-3333-3333-3333-333333333333",
              "name": "Store 1",
              "created_on": "2017-10-15T05:10:11.417553Z"
            },
            {
              "id": "11111111-1111-1111-1111-111111111112",
              "platform_id": "22222222-2222-2222-2222-222222222222",
              "merchant_id": "33333333-3333-3333-3333-333333333333",
              "name": "Store 2",
              "created_on": "2017-06-08T00:44:22.994851Z"
            }
          ],
          "has_more": false
        }
EOD;
        $json = json_decode($str, true);
        $stores = Paginated::fromResponse($json, [], Store::class, $this->getClient()->getStoreBasedContext());
        $this->assertEquals(false, $stores->hasMore);
        $this->assertEquals(2, count($stores->items));
        $this->assertEquals('Store 2', $stores->items[1]->name);
    }
}
