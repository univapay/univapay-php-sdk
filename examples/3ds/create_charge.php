<?php
require_once('vendor/autoload.php');

use Money\Money;
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
);

$token = $client->createToken($paymentMethod);
$charge = $client->createCharge($token->id, Money::JPY(100))->awaitResult(5);

$charge->threeDSToken();
