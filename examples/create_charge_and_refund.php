<?php

require_once('vendor/autoload.php');

use Univapay\UnivapayClient;
use Univapay\Enums\RefundReason;
use Univapay\Resources\Authentication\AppJWT;
use Univapay\Resources\PaymentData\Address;
use Univapay\Resources\PaymentData\PhoneNumber;
use Univapay\Resources\PaymentMethod\CardPayment;
use Money\Money;

$storeAppToken = AppJWT::createToken('token', 'secret');
$client = new UnivapayClient($storeAppToken);
$paymentMethod = new CardPayment(
    'test@test.com',
    'PHP example',
    '4242424242424242',
    '02',
    '2025',
    '123',
    null, // Set TokenType::RECURRING() here for recurring tokens. See TokenType for other token types.
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

$charge = $client->createToken($paymentMethod)->createCharge(Money::USD(1000))->awaitResult();
// Or
$token = $client->createToken($paymentMethod);
// If you are using recurring tokens, you can save the token ID ($token->id) for later use
// The recurring token is unique to the customer's card, so ensure you store it in a way that
// that can be easily referenced later

// If you have saved an existing recurring token ID, replace $token->id with the ID
$charge = $client->createCharge($token->id, Money::USD(1000));
// Optionally specify the number of times to retry until a non waiting status returns.
$charge = $charge->awaitResult(3);
$status = $charge->status; // Check the status of the charge

$refund = $charge
    ->createRefund(
        Money::USD(1000),
        // Please select an appropriate reason. See RefundReason.php for available options
        RefundReason::CUSTOMER_REQUEST(),
        'test',
        ['something' => null]
    )
    ->awaitResult(); // Long polls for the next status change, with a 3s timeout

// Use `fetch` to get the latest data from the API
$refund->fetch();

// Alternatively use `awaitResult` to poll for a non waiting status.
// Optionally specify the number of times to retry until a non waiting status returns.
$refund->awaitResult(3);

// To make an authorization charge and save the charge ID for later
$charge = $client->createCharge($token->id, Money::USD(1000), false);

// Get the charge object from store ID and charge ID
$charge = $client->getCharge($storeAppToken->storeId, $chargeId);
// Capture the charge
$charge->capture(); // Full amount
$charge->capture(Money::USD(500)); // Partial amount
$charge = $charge->awaitResult(3); // Check the charge status
