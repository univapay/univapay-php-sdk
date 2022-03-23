<?php
namespace UnivapayTest\Integration;

use Univapay\Enums\CancelStatus;
use Univapay\Enums\RefundStatus;
use Univapay\Enums\WebhookEvent;
use Money\Money;
use PHPUnit\Framework\TestCase;

class WebhookPayloadTest extends TestCase
{
    use IntegrationSuite;

    public function testChargeWebhookPayloadParse()
    {
        $str = <<<EOD
      {
       "event":"charge_finished",
       "data":{
          "id":"11e756f4-ed34-6152-970d-77c75a0f7890",
          "store_id":"11e746a0-f4f1-dc3a-a472-831414c04dce",
          "transaction_token_id":"11e756f4-e9dc-2c56-970b-2f1c78640cc7",
          "transaction_token_type":"one_time",
          "requested_amount":100,
          "requested_currency":"JPY",
          "requested_amount_formatted":100,
          "charged_amount":100,
          "charged_currency":"JPY",
          "charged_amount_formatted":100,
          "status":"successful",
          "error":null,
          "metadata":{
             "orderId":123456,
             "someString":"abcdefg"
          },
          "mode":"test",
          "type": "normal",
          "created_on":"2017-06-22T02:46:00.972639Z"
       }
    }
EOD;

        $payload = $this->getClient()->parseWebhookData(json_decode($str, true));
        $this->assertEquals(WebhookEvent::CHARGE_FINISHED(), $payload->event);
        $this->assertEquals(Money::JPY(100), $payload->data->requestedAmount);
        $this->assertEquals(['orderId' => 123456, 'someString' => 'abcdefg'], $payload->data->metadata);
    }

    public function testRefundWebhookPayloadParse()
    {
        $str = <<<EOD
          {
            "event": "refund_finished",
            "data": {
              "id": "11ecaa51-018e-a27a-8fa0-ab51010ffaec",
              "charge_id": "11ecaa50-868a-e4ee-9b3d-437680244d9d",
              "store_id": "11e985da-801f-caca-958b-373c9fbae3cd",
              "status": "successful",
              "amount": 100,
              "currency": "JPY",
              "amount_formatted": 100,
              "reason": null,
              "message": null,
              "error": null,
              "metadata": {},
              "mode": "test",
              "created_on": "2022-03-23T02:29:03.683871Z"
            }
          }
EOD;

        $payload = $this->getClient()->parseWebhookData(json_decode($str, true));
        $this->assertEquals(WebhookEvent::REFUND_FINISHED(), $payload->event);
        $this->assertEquals(Money::JPY(100), $payload->data->amount);
        $this->assertEquals(RefundStatus::SUCCESSFUL(), $payload->data->status);
        $this->assertEquals("11ecaa50-868a-e4ee-9b3d-437680244d9d", $payload->data->chargeId);
        $this->assertEquals("11e985da-801f-caca-958b-373c9fbae3cd", $payload->data->storeId);
    }

    public function testCancelWebhookPayloadParse()
    {
        $str = <<<EOD
          {
            "event": "cancel_finished",
            "data": {
              "id": "11ecaa50-883a-1c60-8f53-bf3460612ad4",
              "charge_id": "11ecaa50-868a-e4ee-9b3d-437680244d9d",
              "store_id": "11e985da-801f-caca-958b-373c9fbae3cd",
              "status": "successful",
              "error": null,
              "metadata": {},
              "mode": "test",
              "created_on": "2022-03-23T02:25:40.125629Z"
            }
          }
EOD;

        $payload = $this->getClient()->parseWebhookData(json_decode($str, true));
        $this->assertEquals(WebhookEvent::CANCEL_FINISHED(), $payload->event);
        $this->assertEquals(CancelStatus::SUCCESSFUL(), $payload->data->status);
        $this->assertEquals("11ecaa50-868a-e4ee-9b3d-437680244d9d", $payload->data->chargeId);
        $this->assertEquals("11e985da-801f-caca-958b-373c9fbae3cd", $payload->data->storeId);
    }
}
