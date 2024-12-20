<?php
require_once('vendor/autoload.php');

use Money\Money;
use Univapay\UnivapayClient;
use Univapay\Enums\ThreeDSMode;
use Univapay\Enums\TokenType;
use Univapay\Resources\ThreeDSMPI;
use Univapay\Resources\PaymentThreeDS;
use Univapay\Resources\PaymentData\Address;
use Univapay\Resources\PaymentData\PhoneNumber;
use Univapay\Resources\Authentication\AppJWT;
use Univapay\Resources\PaymentMethod\CardPayment;

$storeAppToken = AppJWT::createToken('app_token', 'secret');
$client = new UnivapayClient($storeAppToken);
$paymentMethod = new CardPayment(
    'test@test.com',
    'PHP example',
    '4242424242424242',
    '02',
    '2030',
    '123',
    TokenType::ONE_TIME(),
    null,
    new Address(
        'test line 1',
        'test line 2',
        'tokyo',
        'tokyo',
        'jp',
        '101-1111'
    ),
    new PhoneNumber(PhoneNumber::JP, '12910298309128')
);
$token = $client->createToken($paymentMethod);

/**
 * Example 1. Create charge with requiring 3DS 
 */

$charge = $client->createCharge(
    $token->id,
    Money::JPY(100),
    true, // automatically capture the charge after 3DS authentication, or set to false to capture manually after 3DS authentication
    null,
    null,
    null,
    null,
    new PaymentThreeDS(
        "https://ec-site.example.com/3ds/complete", // redirect endpoint when 3DS is completed
        null,
        ThreeDSMode::NORMAL(), // check documentation for more about 3DS modes
        null
    )
)->awaitResult(5);
$charge->threeDSIssuerToken(); // information for issuer token for 3DS authentication
// redirect user to issuer's 3DS page
// after 3DS authentication, user will be redirected to the endpoint specified in PaymentThreeDS


/**
 * Example 2. Create charge with authorized 3DS MPI
 */

// creare charge with authorized 3ds transaction token
$charge = $client->createCharge(
    $token->id,
    Money::JPY(100),
    true,
    null,
    null,
    null,
    null,
    new PaymentThreeDS(
        null,
        null,
        null,
        new ThreeDSMPI(
            '1234567890123456789012345678',
            '12',
            '11efbb62-7838-0492-acd7-aaabfef2ee8d',
            '11efbb62-7838-0492-acd7-aaabfef2ee8a',
            '2.2.0',
            'A'
        )
    )
)->awaitResult(5);
