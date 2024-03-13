<?php

require_once('vendor/autoload.php');

use Univapay\UnivapayClient;
use Univapay\Enums\Period;
use Univapay\Enums\SubscriptionPlanType;
use Univapay\Enums\TokenType;
use Univapay\Resources\Authentication\AppJWT;
use Univapay\Resources\PaymentData\Address;
use Univapay\Resources\PaymentData\PhoneNumber;
use Univapay\Resources\PaymentMethod\CardPayment;
use Univapay\Resources\Subscription\ScheduleSettings;
use Univapay\Resources\Subscription\SubscriptionPlan;
use Money\Money;

$storeAppToken = AppJWT::createToken('token', 'secret');
$client = new UnivapayClient($storeAppToken);
$paymentMethod = new CardPayment(
    'test@test.com',
    'PHP example',
    '4242424242424242',
    '02',
    '2030',
    '123',
    TokenType::SUBSCRIPTION(), // Set TokenType::RECURRING() here for recurring tokens. See TokenType for other token types.
    null,
    new Address(
        'test line 1',
        'test line 2',
        'test state',
        'test city',
        'jp',
        '101-1111'
    ),
    new PhoneNumber(PhoneNumber::JP, '12910298309128')
);

$charge = $client->createToken($paymentMethod)->createSubscription(
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
