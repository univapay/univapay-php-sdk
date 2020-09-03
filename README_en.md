# UnivaPay PHP SDK

![Github Actions CI](https://github.com/univapay/univapay-php-sdk/workflows/PHP%20lint%20&%20test/badge.svg)

This PHP SDK provides a convenient way to integrate your services with the UnivaPay payments gateway.

*[日本語](README.md)*

## Requirements

- PHP >= 5.6
- Composer
- npm (dev only)
- UnivaPay store application token _and/or_ merchant application token

## Installation

```shell
composer require univapay/php-sdk
```

## Usage

```php
use UnivapayUnivapayClient;
use UnivapayUnivapayClientOptions;
use UnivapayRequestsHandlersRateLimitHandler;

$client = new UnivapayClient(AppJWT::createToken('token', 'secret'));

// For more options, create and modify the client options object before instantiating the client
// See UnivapayClientOptions for full options list
$clientOptions = new UnivapayClientOptions();
$clientOptions->rateLimitHandler = new RateLimitHandler(5, 2);
$client = new UnivapayClient(AppJWT::createToken('token', 'secret'), $clientOptions);

// See the examples folder for usage examples
```

### Application Tokens

Both store and merchant type application tokens are supported by this SDK. Apart from creating transaction tokens and charges which require a store type token, all other features are supported by both token types.

### Money models
This SDK uses the `moneyphp` library to model amounts and currency. Please refer to the [documentation](http://moneyphp.org/en/latest/index.html) for more details.
All currencies and amounts will be automatically converted to `Currency` and `Money` objects. Only formatted amounts (denoted by the `.*Formatted` key) will be in string form.

```php
use MoneyCurrency;
use MoneyMoney;
use UnivapayPaymentMethodCardPayment;

$paymentMethod = new CardPayment(...);
$charge = $client
    ->createToken($paymentMethod)
    ->createCharge(Money::USD(1000));

$charge->currency === new Currency('USD'); // true
$charge->requestAmount === new Money(1000, $charge->currency); // true
```

### Enumerators

As PHP has no native built in enumeration support, we provide a class called `TypedEnum` to provide type safety when working with enumerators. Each enumerator class is final and extends `TypedEnum` to provide static functions that operate similarly to enumerators in other languages like Java. A enum classes can be found in the `UnivapayEnums` namespace.

_By default, if the value is not specified during creation, it will be snake-cased from the name_

```php
use UnivapayEnumsChargeStatus;

$values = ChargeStatus::findValues(); // Get a list of all names and values in the enumerator
$chargeStatus = ChargeStatus::PENDING(); // Note the braces at the end
$chargeStatus->getValue() === 'pending'; // true
$chargeStatus === ChargeStatus::fromValue('pending'); // true
// Also works for switch statements
switch ($chargeStatus) {
    case ChargeStatus::PENDING():
        // Do something
        break;
    // ...
}
```

### Updating resource models
To update/refresh any resource models (model classes that extends `Resource`)

```php
$charge->fetch();
```

### Long polling
The following resources supports long polling to wait for the next status change:
- `Charge`
- `Refund`
- `Cancel`
- `Subscription`

This is useful since these requests initially returns with a `PENDING` status. Long polling allows you to fetch the updated model when the resource has changed its status. If no changes occurs within 3 seconds, it will return the resource at that state.

```php
$charge = $client
    ->createCharge($token->id, Money::USD(1000)) // $charge->status == PENDING
    ->awaitResult(); // $charge->status == SUCCESSFUL
```

### Lists and pagination

All list functions in the SDK returns as a `Paginated` object in descending order of their creation time. When passing in parameters through an array, be careful to ensure your input matches the expected type, otherwise an `InvalidArgumentException` will be thrown.

```php
use InvalidArgumentException;
use UnivapayEnumsCursorDirection;

try {
    $transactionList = $client->listTransactionsByOptions([
        'from' => date_create('-1 week'),
        'to' => date_create('+1 week')
    ]);
} catch (InvalidArgumentException $error) {
    // When input parameters does not correspond to the correct type
}

$transactions = $transactionList->items; // Default limit per page = 10 items

if ($transactionList->hasMore) {
    $transactionList = $transactionList->getNext(); // The list does not mutate internally
    $transactions = array_merge($transactions, $transactionList->items);
}

$firstTenItems = $client->listTransactionsByOptions([
    'from' => date_create('-1 week'),
    'to' => date_create('+1 week'),
    'cursor_direction' => CursorDirection::ASC()
]);
```

### Request/Response Handlers

For advance use cases that require additional modification or reaction to responses prior to parsing the data into objects. The SDK provides a `RateLimitHandler` that throttles requests based on back pressure from the API (this is implemented by default in `UnivapayClientOptions->rateLimitHandler`). In addition, a `BasicRetryHandler` is also provided to catch and filter certain exceptions for retry. To specify an exception to catch:

```php
use UnivapayRequestsHandlersBasicRetryHandler;

$subscriptionTokenRetryHandler = new BasicRetryHandler(
    UnivapayResourceConflictError::class,
    5, // Tries 5 times
    2, // At 2 seconds interval
    // More specific filtering based on the error, takes in the error as the first parameter
    // return true to retry, false to ignore.
    function (UnivapayResourceConflictError $error) {
        return $error->code === 'NON_UNIQUE_ACTIVE_TOKEN';
    }
);
$client->addHandlers($subscriptionTokenRetryHandler);

// To reset or to clear and add new handlers from scratch
// The rateLimitHandler will be automatically added from UnivapayClientOptions
$client->setHandlers($subscriptionTokenRetryHandler);
```

## SDK Development

Building:
```shell
composer install
npm install

# Optionally
npm install -g grunt
```

Code formatting:
```shell
grunt phpcs
```

Tests:

The following env vars are required when running the tests:

- `UNIVAPAY_PHP_TEST_TOKEN` - This should be a `test` mode token
- `UNIVAPAY_PHP_TEST_SECRET`
- `UNIVAPAY_PHP_TEST_ENDPOINT` - This would point to a local API instance or a staging instance

```shell
grunt phpunit
```
_Note: Github Actions only runs on branches that has an open PR_
