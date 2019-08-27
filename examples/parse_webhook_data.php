<?php

require_once('vendor/autoload.php');

use Univapay\UnivapayClient;

$client = new UnivapayClient(AppJWT::createToken('token', 'secret'));

$data = <<<EOD
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
}"
EOD;
$client->parseWebhookData(json_decode($data, true));
