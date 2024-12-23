<?php
require_once('vendor/autoload.php');

use Money\Money;
use Univapay\UnivapayClient;
use Univapay\Enums\Period;
use Univapay\Enums\SubscriptionPlanType;
use Univapay\Enums\TokenType;
use Univapay\Resources\Authentication\AppJWT;
use Univapay\Resources\PaymentData\Address;
use Univapay\Resources\PaymentData\PhoneNumber;
use Univapay\Resources\PaymentData\TokenThreeDS;
use Univapay\Resources\PaymentMethod\CardPayment;
use Univapay\Resources\Subscription\ScheduleSettings;
use Univapay\Resources\Subscription\SubscriptionPlan;

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

$token = $client->createToken($paymentMethod)->awaitResult();
// Fetch information for issuer token for 3DS authentication and redirect user to 3DS authentication page
// after 3DS authentication is completed, user will be redirected to the endpoint specified in PaymentThreeDS
$charge->threeDSIssuerToken();

$subscription = $client->createSubscription(
    $token->id,
    Money::JPY(20000),
    Period::QUARTERLY(),
    Money::JPY(15000),
    new ScheduleSettings(
        date_create('+1 month') // Date to start the subscription after initial charge
    ),
    new SubscriptionPlan(
        SubscriptionPlanType::FIXED_CYCLES(),
        21 // The number of cycles including the first cycle of initial amount
    )
)->awaitResult(5);
