<?php

require_once('vendor/autoload.php');

use Univapay\UnivapayClient;
use Univapay\Enums\RefundReason;
use Univapay\Resources\Authentication\AppJWT; 
use Univapay\Resources\PaymentData\Address;
use Univapay\Resources\PaymentData\PhoneNumber;
use Univapay\Resources\PaymentMethod\CardPayment;
use Money\Money;

$client = new UnivapayClient(AppJWT::createToken('token', 'secret'));
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
$charge = $charge->awaitResult();
$status = $charge->status; // Check the status of the charge

$refund = $charge
    ->createRefund(Money::USD(1000), RefundReason::FRAUD(), 'test', ['something' => null])
    ->awaitResult(); // Long polls for the next status change, with a 3s timeout

// Use fetch to fetch the latest data from the API
$refund->fetch();
