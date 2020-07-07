<?php

namespace Univapay\Resources;

use DateTime;
use Composer\DependencyResolver\Request;
use Univapay\Requests\RequestContext;
use Univapay\Resources\Configuration\Configuration;
use Univapay\Resources\Mixins\GetCharges;
use Univapay\Resources\Mixins\GetSubscriptions;
use Univapay\Resources\Mixins\GetTransactions;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\RequesterUtils;
use Univapay\Utility\Json\JsonSchema;

class Store extends Resource
{
    use Jsonable;
    use GetCharges, GetSubscriptions, GetTransactions {
        GetCharges::validate insteadof GetSubscriptions, GetTransactions;
    }

    public $name;
    public $createdOn;
    public $configuration;

    public function __construct(
        $id,
        $name,
        DateTime $createdOn,
        $configuration,
        RequestContext $context = null
    ) {
        parent::__construct($id, $context);
        $this->name = $name;
        $this->createdOn = $createdOn;
        $this->configuration = $configuration;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('configuration', false, Configuration::getSchema()->getParser())
            ->upsert('created_on', true, FormatterUtils::of('getDateTime'));
    }

    public function getCharge($chargeId)
    {
        $context = $this->getIdContext()->appendPath(['charges', $chargeId]);
        return RequesterUtils::executeGet(Charge::class, $context);
    }

    public function getSubscription($subscriptionId)
    {
        $context = $this->getIdContext()->appendPath(['subscriptions', $subscriptionId]);
        return RequesterUtils::executeGet(Subscription::class, $context);
    }

    public function getCustomerId($localCustomerId)
    {
        return RequesterUtils::executePost(
            null,
            $this->getIdContext()->appendPath('create_customer_id'),
            ['customer_id' => $localCustomerId]
        )['customer_id'];
    }

    protected function getSubscriptionContext()
    {
        return $this->getIdContext()->appendPath('subscriptions');
    }

    protected function getTransactionContext()
    {
        return $this->getIdContext()->appendPath('transaction_history');
    }

    protected function getChargeContext()
    {
        return $this->getIdContext()->appendPath('charges');
    }
}
