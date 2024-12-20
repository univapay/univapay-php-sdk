<?php
require_once('vendor/autoload.php');

use Money\Money;
use Univapay\UnivapayClient;
use Univapay\Enums\Period;
use Univapay\Enums\SubscriptionPlanType;
use Univapay\Enums\ThreeDSMode;
use Univapay\Enums\TokenType;
use Univapay\Resources\Authentication\AppJWT;
use Univapay\Resources\PaymentData\Address;
use Univapay\Resources\PaymentData\PhoneNumber;
use Univapay\Resources\PaymentData\TokenThreeDS;
use Univapay\Resources\PaymentMethod\CardPayment;
use Univapay\Resources\PaymentThreeDS;
use Univapay\Resources\Subscription\ScheduleSettings;
use Univapay\Resources\Subscription\SubscriptionPlan;

/**
 * Example 1: of creating a subscription with a 3DS on Recurring Token
 */
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
        null,
        "https://ec-site.example.com/3ds/complete" // redirect url when 3ds transaction is completed
    )
);

$token = $client->createToken($paymentMethod)->awaitResult();
$token->threeDSIssuerToken(); // information issuer token for 3DS authentication
// Complete the 3DS transaction process using the provided issuer token
// After completing the 3DS transaction, redirection will occur based on the URL provided in the TokenThreeDS object

// Then create a charge with the token
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
    ),
    null,
    null
)->awaitResult(5);


/**
 * Example 2: of creating a subscription requiring 3DS on Subscription Token
 */
$storeAppToken = AppJWT::createToken('token', 'secret');
$client = new UnivapayClient($storeAppToken);
$paymentMethod = new CardPayment(
    'test@test.com',
    'PHP example',
    '4242424242424242',
    '02',
    '2030',
    '123',
    TokenType::SUBSCRIPTION(),
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

$token = $client->createToken($paymentMethod)->awaitResult();
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
    ),
    null,
    null,
    new PaymentThreeDS(
        "https://ec-site.example.com/3ds/complete", // redirect endpoint when 3DS is completed
        null,
        ThreeDSMode::NORMAL() // check documentation for more about 3DS modes
    )
);

$charge = $client->getLatestChargeForSubscription(
    $subscription->storeId,
    $subscription->id
)->awaitResult(5);
$charge->threeDSIssuerToken(); // issuer token for 3DS authentication
// redirect user to issuer's 3DS page
// after 3DS authentication, user will be redirected to the endpoint specified in PaymentThreeDS

