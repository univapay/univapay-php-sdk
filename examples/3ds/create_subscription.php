<?php
require_once('vendor/autoload.php');

use Money\Money;
use Univapay\UnivapayClient;
use Univapay\Enums\ChargeStatus;
use Univapay\Enums\Period;
use Univapay\Enums\SubscriptionPlanType;
use Univapay\Enums\ThreeDSMode;
use Univapay\Enums\TokenType;
use Univapay\Resources\PaymentThreeDS;
use Univapay\Resources\Authentication\AppJWT;
use Univapay\Resources\PaymentData\Address;
use Univapay\Resources\PaymentData\PhoneNumber;
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

/**
 * Example 1: of creating a subscription requiring 3DS on Subscription Token
 */
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
    PaymentThreeDS::withThreeDS(
        "https://ec-site.example.com/3ds/complete", // redirect endpoint when 3DS is completed
        ThreeDSMode::NORMAL() // for more details, refer to the Univapay documentation on 3DS modes.
    )
);

$charge = $client->getLatestChargeForSubscription(
    $subscription->storeId,
    $subscription->id
);

$charge = $charge->awaitResult(5);
switch ($charge->status) {
    case ChargeStatus::AWAITING():
        // Fetch information for issuer token for 3DS authentication and redirect user to 3DS authentication page
        // after 3DS authentication is completed, user will be redirected to the endpoint specified in PaymentThreeDS
        $charge->threeDSIssuerToken();
        break;
    case ChargeStatus::SUCCESSFUL():
        // continue with payment flow
    case ChargeStatus::PENDING():
    case ChargeStatus::FAILED():
    case ChargeStatus::ERROR():
        // implement error handling
}

/**
 * Example 2. Create subscription with authorized 3DS MPI
 */
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
    PaymentThreeDS::withThreeDSMPI(
        '1234567890123456789012345678',
        '12',
        '11efbb62-7838-0492-acd7-aaabfef2ee8d',
        '11efbb62-7838-0492-acd7-aaabfef2ee8a',
        '2.2.0',
        'A'
    )
);
