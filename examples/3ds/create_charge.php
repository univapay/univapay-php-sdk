<?php
require_once('vendor/autoload.php');

use Univapay\UnivapayClient;
use Univapay\UnivapayClientOptions;
use Univapay\Resources\PaymentData\Address;
use Univapay\Resources\PaymentData\PhoneNumber;
use Univapay\Resources\Authentication\AppJWT;
use Univapay\Resources\PaymentMethod\CardPayment;
use Univapay\Enums\TokenType;

$storeAppToken = AppJWT::createToken('app_token', 'secret');
$clientOptions = new UnivapayClientOptions();
$client = new UnivapayClient($storeAppToken, $clientOptions);

$paymentMethod = new CardPayment(
    'test@test.com',
    'PHP example',
    '4242424242424242',
    '02',
    '2030',
    '123',
    TokenType::ONE_TIME(), // Set TokenType::RECURRING() here for recurring tokens. See TokenType for other token types.
    null,
    new Address(
        'test line 1',
        'test line 2',
        'tokyo',
        'tokyo',
        'jp',
        '101-1111'
    ),
    new PhoneNumber(PhoneNumber::JP, '12910298309128'),
    null,
    null,
    null,
    null,
);

$token = $client->createToken($paymentMethod);
$charge = $client->getCharge("11ef4801-666d-9d54-ba7c-471de98b40b6", "11ef8210-1bb6-dc8c-a1eb-97d81d6c1d46")->awaitResult();
$charge->issuerTokenThreeDS();
