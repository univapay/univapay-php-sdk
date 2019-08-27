<?php

require_once('vendor/autoload.php');

use Univapay\UnivapayClient;

$client = new UnivapayClient(AppJWT::createToken('token', 'secret'));

$client->getMe();
$stores = $client->listStores();
$store = current($stores->items)->fetch();
$client->getStore($store->id);
$accounts = $client->listBankAccounts();
$account = current($accounts->items)->fetch();
$client->getBankAccount($account->id);
$transfers = $client->listTransfers();
if (sizeof($transfers->items) > 0) {
    $transfer = current($transfers->items)->fetch();
    $client->getTransfer($transfer->id);
}
$charges = $client->listCharges();
$charge = current($charges->items)->fetch();
$client->getCharge($charge->storeId, $charge->id);
$refunds = $charge->listRefunds();
$subscriptions = $client->listSubscriptions();
if (sizeof($subscriptions->items) > 0) {
    $subscription = current($subscriptions->items)->fetch();
    $client->getSubscription($subscription->storeId, $subscription->id);
}
$client->listTransactions()->getNext();
