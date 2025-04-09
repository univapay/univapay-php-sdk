<?php

namespace Univapay;

use DateTime;
use Exception;
use Univapay\Enums\Field;
use Univapay\Enums\PaymentType;
use Univapay\Enums\Period;
use Univapay\Enums\Reason;
use Univapay\Enums\TokenType;
use Univapay\Enums\WebhookEvent;
use Univapay\Errors\UnivapayInvalidWebhookData;
use Univapay\Errors\UnivapaySDKError;
use Univapay\Errors\UnivapayUnknownWebhookEvent;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Requests\HttpRequester;
use Univapay\Requests\RequestContext;
use Univapay\Requests\Handlers\RequestHandler;
use Univapay\Resources\BankAccount;
use Univapay\Resources\Cancel;
use Univapay\Resources\Charge;
use Univapay\Resources\CheckoutInfo;
use Univapay\Resources\Merchant;
use Univapay\Resources\Redirect;
use Univapay\Resources\Refund;
use Univapay\Resources\Store;
use Univapay\Resources\Subscription;
use Univapay\Resources\PaymentThreeDS;
use Univapay\Resources\TransactionToken;
use Univapay\Resources\Transfer;
use Univapay\Resources\WebhookPayload;
use Univapay\Resources\Authentication\AppJWT;
use Univapay\Resources\Authentication\StoreAppJWT;
use Univapay\Resources\Mixins\GetBankAccounts;
use Univapay\Resources\Mixins\GetCharges;
use Univapay\Resources\Mixins\GetStores;
use Univapay\Resources\Mixins\GetSubscriptions;
use Univapay\Resources\Mixins\GetTransactions;
use Univapay\Resources\Mixins\GetTransactionTokens;
use Univapay\Resources\Mixins\GetTransfers;
use Univapay\Resources\PaymentMethod\PaymentMethod;
use Univapay\Resources\Subscription\InstallmentPlan;
use Univapay\Resources\Subscription\ScheduledPayment;
use Univapay\Resources\Subscription\ScheduleSettings;
use Univapay\Resources\Subscription\SubscriptionPlan;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\RequesterUtils;
use Money\Money;

class UnivapayClient
{
    use GetBankAccounts, GetCharges, GetStores, GetSubscriptions, GetTransactions, GetTransactionTokens, GetTransfers {
        GetCharges::validate insteadof GetBankAccounts,
        GetStores,
        GetSubscriptions,
        GetTransactions,
        GetTransactionTokens,
        GetTransfers;
    }

    private $appToken;
    private $clientOptions;
    private $requester;

    public function __construct(
        AppJWT $appToken,
        UnivapayClientOptions $clientOptions = null
    ) {
        if (is_null($clientOptions)) {
            $clientOptions = new UnivapayClientOptions();
        }
        $this->appToken = $appToken;
        $this->clientOptions = $clientOptions;
        $this->requester = new HttpRequester(...$this->clientOptions->getRequestHandlers());
    }

    public function getContext()
    {
        return new RequestContext($this->requester, $this->clientOptions->endpoint, '/', $this->appToken);
    }

    public function getStoreBasedContext()
    {
        if (!$this->appToken instanceof StoreAppJWT) {
            throw new UnivapaySDKError(Reason::REQUIRES_STORE_APP_TOKEN());
        }
        return $this->getContext();
    }

    public function addHandlers(RequestHandler ...$handlers)
    {
        $this->requester->addHandlers(...$handlers);
    }

    public function setHandlers(RequestHandler ...$handlers)
    {
        $this->requester->setHandlers(...array_merge($this->clientOptions->getRequestHandlers(), $handlers));
    }

    public function getMe()
    {
        return RequesterUtils::executeGet(
            Merchant::class,
            $this->getContext()->withPath('me')
        );
    }

    public function getCheckoutInfo()
    {
        return RequesterUtils::executeGet(
            CheckoutInfo::class,
            $this->getStoreBasedContext()->withPath('checkout_info')
        );
    }

    public function getStore($id)
    {
        $context = $this->getStoreContext()->appendPath($id);
        return RequesterUtils::executeGet(Store::class, $context);
    }

    public function getBankAccount($id)
    {
        $context = $this->getBankAccountContext()->appendPath($id);
        return RequesterUtils::executeGet(BankAccount::class, $context);
    }

    public function createToken(PaymentMethod $payment, $localCustomerId = null)
    {
        $context = $this->getStoreBasedContext()->withPath('tokens');
        if (isset($localCustomerId) && $payment->type === TokenType::RECURRING()) {
            $customerId = $this->getCustomerId($localCustomerId);
            if (!isset($payment->metadata)) {
                $payment->metadata = [];
            }
            $payment->metadata += ['gopay-customer-id' => $customerId];
        }

        return RequesterUtils::executePost(TransactionToken::class, $context, $payment);
    }

    public function getTransactionToken($transactionTokenId)
    {
        $context = $this->getStoreBasedContext()->withPath([
            'stores',
            $this->appToken->storeId,
            'tokens',
            $transactionTokenId
        ]);
        return RequesterUtils::executeGet(TransactionToken::class, $context);
    }

    public function createCharge(
        $transactionTokenId,
        Money $money,
        $capture = null,
        DateTime $captureAt = null,
        array $metadata = null,
        $onlyDirectCurrency = null,
        Redirect $redirect = null,
        PaymentThreeDS $paymentThreeDS = null
    ) {
        return $this
            ->getTransactionToken($transactionTokenId)
            ->createCharge(
                $money,
                $capture,
                $captureAt,
                $metadata,
                $onlyDirectCurrency,
                $redirect,
                $paymentThreeDS
            );
    }

    public function getCharge($storeId, $chargeId)
    {
        $context = $this->getContext()->withPath(['stores', $storeId, 'charges', $chargeId]);
        return RequesterUtils::executeGet(Charge::class, $context);
    }

    public function getLatestChargeForSubscription($storeId, $subscriptionId)
    {
        $context = $this->getContext()->withPath(
            [
                'stores',
                $storeId,
                'subscriptions',
                $subscriptionId,
                'charges',
                'latest'
            ]
        );
        return RequesterUtils::executeGet(Charge::class, $context);
    }

    public function createSubscription(
        $transactionTokenId,
        Money $money,
        Period $period,
        Money $initialAmount = null,
        ScheduleSettings $scheduleSettings = null,
        SubscriptionPlan $subscriptionPlan = null,
        InstallmentPlan $installmentPlan = null,
        array $metadata = null,
        PaymentThreeDS $paymentThreeDS = null
    ) {
        return $this
            ->getTransactionToken($transactionTokenId)
            ->createSubscription(
                $money,
                $period,
                $initialAmount,
                $scheduleSettings,
                $subscriptionPlan,
                $installmentPlan,
                $metadata,
                null, // only direct currency
                null, // first charge authorization only
                null, // first charge capture after
                null, // cyclical period
                $paymentThreeDS
            );
    }

    public function getSubscription($storeId, $subscriptionId)
    {
        $context = $this->getContext()->withPath(['stores', $storeId, 'subscriptions', $subscriptionId]);
        return RequesterUtils::executeGet(Subscription::class, $context);
    }

    public function createSubscriptionSimulation(
        PaymentType $paymentType,
        Money $amount,
        Period $period,
        Money $initialAmount = null,
        ScheduleSettings $scheduleSettings = null,
        SubscriptionPlan $subscriptionPlan = null,
        InstallmentPlan $installmentPlan = null
    ) {
        $payload = $amount->jsonSerialize() + [
            'payment_type' => $paymentType->getValue(),
            'period' => $period->getValue(),
            'schedule_settings' => $scheduleSettings,
            'subscription_plan' => $subscriptionPlan,
            'installment_plan' => $installmentPlan
        ];
        if (isset($initialAmount)) {
            if ($initialAmount->isNegative()) {
                throw new UnivapayValidationError(Field::INITIAL_AMOUNT(), Reason::INVALID_FORMAT());
            } else {
                $payload += $initialAmount->jsonSerialize();
            }
        }

        $context = $this->getStoreBasedContext()->appendPath(['subscriptions', 'simulate_plan']);
        return RequesterUtils::executePostSimpleList(
            ScheduledPayment::class,
            $context,
            FunctionalUtils::stripNulls($payload)
        );
    }

    public function getTransfer($id)
    {
        $context = $this->getTransferContext()->appendPath($id);
        return RequesterUtils::executeGet(Transfer::class, $context);
    }

    public function parseWebhookData($data)
    {
        try {
            $event = WebhookEvent::fromValue($data['event']);
            $parser = null;
            switch ($event) {
                case WebhookEvent::TOKEN_CREATED():
                case WebhookEvent::TOKEN_UPDATED():
                case WebhookEvent::TOKEN_CVV_AUTH_UPDATED():
                case WebhookEvent::RECURRING_TOKEN_DELETED():
                    $parser = TransactionToken::getContextParser($this->getTransactionTokenContext());
                    break;

                case WebhookEvent::CHARGE_UPDATED():
                case WebhookEvent::CHARGE_FINISHED():
                    $parser = Charge::getContextParser($this->getChargeContext());
                    break;

                case WebhookEvent::SUBSCRIPTION_PAYMENT():
                case WebhookEvent::SUBSCRIPTION_COMPLETED():
                case WebhookEvent::SUBSCRIPTION_FAILURE():
                case WebhookEvent::SUBSCRIPTION_CANCELED():
                case WebhookEvent::SUBSCRIPTION_SUSPENDED():
                    $parser = Subscription::getContextParser($this->getSubscriptionContext());
                    break;
                
                case WebhookEvent::REFUND_FINISHED():
                    $parser = Refund::getContextParser($this->getStoreBasedContext());
                    break;

                case WebhookEvent::CANCEL_FINISHED():
                    $parser = Cancel::getContextParser($this->getStoreBasedContext());
                    break;

                case WebhookEvent::TRANSFER_CREATED():
                case WebhookEvent::TRANSFER_UPDATED():
                case WebhookEvent::TRANSFER_FINALIZED():
                    $parser = Transfer::getContextParser($this->getTransferContext());
                    break;
            }
            return new WebhookPayload($event, $parser($data['data']));
        } catch (OutOfRangeException $exception) {
            throw new UnivapayUnknownWebhookEvent($data['event']);
        } catch (Exception $exception) {
            throw new UnivapayInvalidWebhookData($data);
        }
    }

    protected function getCustomerId($localCustomerId)
    {
        return $this->getStore($this->appToken->storeId)->getCustomerId($localCustomerId);
    }

    protected function getBankAccountContext()
    {
        return $this->getContext()->withPath('bank_accounts');
    }

    protected function getChargeContext()
    {
        return $this->getContext()->withPath('charges');
    }

    protected function getStoreContext()
    {
        return $this->getContext()->withPath('stores');
    }

    protected function getSubscriptionContext()
    {
        return $this->getContext()->withPath('subscriptions');
    }

    protected function getTransactionTokenContext()
    {
        return $this->getStoreBasedContext()->withPath('tokens');
    }

    protected function getTransactionContext()
    {
        return $this->getContext()->withPath('transaction_history');
    }

    protected function getTransferContext()
    {
        return $this->getContext()->withPath('transfers');
    }
}
