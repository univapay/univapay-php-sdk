<?php
require_once('vendor/autoload.php');

use Univapay\UnivapayClient;
use Univapay\Enums\ThreeDSStatus;
use Univapay\Enums\TokenType;
use Univapay\Resources\Authentication\AppJWT;
use Univapay\Resources\PaymentData\Address;
use Univapay\Resources\PaymentData\PhoneNumber;
use Univapay\Resources\PaymentData\TokenThreeDS;
use Univapay\Resources\PaymentMethod\CardPayment;

$storeAppToken = AppJWT::createToken('token', 'secret');
$client = new UnivapayClient($storeAppToken);
$paymentMethod = new CardPayment(
    'test@test.com',
    'PHP example',
    '4242424242424242',
    '02',
    '2030',
    '123',
    TokenType::RECURRING(),
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
    new TokenThreeDS(
        true,
        "https://ec-site.example.com/3ds/complete" // redirect url when 3ds transaction is completed
    )
);

$token = $client->createToken($paymentMethod);

$token = $token->awaitResult(5);
switch ($token->data->threeDS->status) {
    case ThreeDSStatus::AWAITING():
        // Fetch information for issuer token for 3DS authentication and redirect user to 3DS authentication page
        // after 3DS authentication is completed, user will be redirected to the endpoint specified in PaymentThreeDS
        $token->threeDSIssuerToken();
        break;
    case ThreeDSStatus::SUCCESSFUL():
        // continue with payment flow
    case ThreeDSStatus::PENDING():
    case ThreeDSStatus::FAILED():
    case ThreeDSStatus::ERROR():
        // implement error handling
}
