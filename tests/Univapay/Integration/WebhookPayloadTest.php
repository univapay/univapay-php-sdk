<?php
namespace UnivapayTest\Integration;

use Univapay\Enums\WebhookEvent;
use Money\Money;
use PHPUnit\Framework\TestCase;

class WebhookPayloadTest extends TestCase
{
    use IntegrationSuite;

    public function testWebhookPayloadParse()
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
          "created_on":"2017-06-22T02:46:00.972639Z"
       }
    }
EOD;

        $payload = $this->getClient()->parseWebhookData(json_decode($str, true));
        $this->assertEquals(WebhookEvent::CHARGE_FINISHED(), $payload->event);
        $this->assertEquals(Money::JPY(100), $payload->data->requestedAmount);
        $this->assertEquals(['orderId' => 123456, 'someString' => 'abcdefg'], $payload->data->metadata);
    }
}
