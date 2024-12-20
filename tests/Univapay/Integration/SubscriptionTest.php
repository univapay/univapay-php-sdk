<?php
namespace UnivapayTest\Integration;

use DateInterval;
use DateTime;
use DateTimeZone;
use Univapay\Enums\AppTokenMode;
use Univapay\Enums\CallMethod;
use Univapay\Enums\InstallmentPlanType;
use Univapay\Enums\PaymentType;
use Univapay\Enums\Period;
use Univapay\Enums\SubscriptionPlanType;
use Univapay\Enums\SubscriptionStatus;
use Univapay\Enums\ThreeDSMode;
use Univapay\Enums\TokenType;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Resources\Paginated;
use Univapay\Resources\PaymentThreeDS;
use Univapay\Resources\SimpleList;
use Univapay\Resources\Subscription;
use Univapay\Resources\ThreeDSMPI;
use Univapay\Resources\Subscription\InstallmentPlan;
use Univapay\Resources\Subscription\ScheduledPayment;
use Univapay\Resources\Subscription\ScheduleSettings;
use Univapay\Resources\Subscription\SubscriptionPlan;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends testCase
{
    use IntegrationSuite;

    public function testSubscriptionWithPeriodParse()
    {
        $str = <<<EOD
        {
            "id": "11111111-1111-1111-1111-111111111111",
            "store_id": "22222222-2222-2222-2222-222222222222",
            "transaction_token_id": "33333333-3333-3333-3333-333333333333",
            "amount": 1000,
            "currency": "JPY",
            "amount_formatted": 1000,
            "period": "monthly",
            "initial_amount": 100,
            "initial_amount_formatted": 100,
            "schedule_settings": {
                "start_on": "2017-07-31",
                "zone_id": "Asia/Tokyo",
                "preserve_end_of_month": true,
                "retry_interval": "P5D"
            },
            "next_payment": {
                "id": "11e893e1-2842-3cea-b0a8-47819043c1eb",
                "due_date": "2018-08-30",
                "zone_id": "Asia/Tokyo",
                "amount": 1000,
                "currency": "JPY",
                "amount_formatted": 1000,
                "is_paid": false,
                "is_last_payment": false,
                "created_on": "2018-07-31T10:13:08.715295Z",
                "updated_on": "2018-07-31T10:13:08.715295Z"
            },
            "first_charge_authorization_only": true,
            "first_charge_capture_after": "PT5H",
            "payments_left": 9,
            "status": "canceled",
            "subscription_plan": {
                "plan_type": "fixed_cycle_amount",
                "fixed_cycle_amount": 1000
            },
            "amount_left": 5000,
            "amount_left_formatted": 5000,
            "metadata": {},
            "mode": "test",
            "created_on": "2017-07-04T06:06:05.580391Z"
        }
EOD;

        $json = json_decode($str, true);
        $subscription = Subscription::getSchema()->parse($json, [$this->getClient()->getStoreBasedContext()]);
        $this->assertEquals('11111111-1111-1111-1111-111111111111', $subscription->id);
        $this->assertEquals('22222222-2222-2222-2222-222222222222', $subscription->storeId);
        $this->assertEquals('33333333-3333-3333-3333-333333333333', $subscription->transactionTokenId);
        $this->assertEquals(Money::JPY(1000), $subscription->amount);
        $this->assertEquals(1000, $subscription->amountFormatted);
        $this->assertEquals(new Currency('JPY'), $subscription->currency);
        $this->assertEquals(Period::MONTHLY(), $subscription->period);
        $this->assertEquals(Money::JPY(100), $subscription->initialAmount);
        $this->assertEquals(100, $subscription->initialAmountFormatted);
        $this->assertEquals(date_create('2017-07-31'), $subscription->scheduleSettings->startOn);
        $this->assertEquals(new DateTimeZone('Asia/Tokyo'), $subscription->scheduleSettings->zoneId);
        $this->assertTrue($subscription->scheduleSettings->preserveEndOfMonth);
        $this->assertEquals(new DateInterval('P5D'), $subscription->scheduleSettings->retryInterval);
        $this->assertEquals(SubscriptionStatus::CANCELED(), $subscription->status);
        $this->assertEquals(AppTokenMode::TEST(), $subscription->mode);
        $this->assertInstanceOf(ScheduledPayment::class, $subscription->nextPayment);
        $this->assertEquals(date_create('2017-07-04T06:06:05.580391Z'), $subscription->createdOn);
        $this->assertEquals(SubscriptionPlanType::FIXED_CYCLE_AMOUNT(), $subscription->subscriptionPlan->planType);
        $this->assertEquals(Money::JPY(1000), $subscription->subscriptionPlan->fixedCycleAmount);
        $this->assertEquals('9', $subscription->paymentsLeft);
        $this->assertEquals(Money::JPY(5000), $subscription->amountLeft);
        $this->assertEquals(5000, $subscription->amountLeftFormatted);
        $this->assertTrue($subscription->firstChargeAuthorizationOnly);
        $this->assertEquals(new DateInterval('PT5H'), $subscription->firstChargeCaptureAfter);
    }

    public function testSubscriptionWithCyclicalPeriodParse()
    {
        $str = <<<EOD
        {
            "id": "11111111-1111-1111-1111-111111111111",
            "store_id": "22222222-2222-2222-2222-222222222222",
            "transaction_token_id": "33333333-3333-3333-3333-333333333333",
            "amount": 1000,
            "currency": "JPY",
            "amount_formatted": 1000,
            "cyclical_period": "P15D",
            "initial_amount": 100,
            "initial_amount_formatted": 100,
            "schedule_settings": {
                "start_on": "2017-07-31",
                "zone_id": "Asia/Tokyo",
                "preserve_end_of_month": true,
                "retry_interval": "P5D"
            },
            "next_payment": {
                "id": "11e893e1-2842-3cea-b0a8-47819043c1eb",
                "due_date": "2018-08-30",
                "zone_id": "Asia/Tokyo",
                "amount": 1000,
                "currency": "JPY",
                "amount_formatted": 1000,
                "is_paid": false,
                "is_last_payment": false,
                "created_on": "2018-07-31T10:13:08.715295Z",
                "updated_on": "2018-07-31T10:13:08.715295Z"
            },
            "first_charge_authorization_only": true,
            "first_charge_capture_after": "PT5H",
            "payments_left": 9,
            "status": "canceled",
            "subscription_plan": {
                "plan_type": "fixed_cycle_amount",
                "fixed_cycle_amount": 1000
            },
            "amount_left": 5000,
            "amount_left_formatted": 5000,
            "metadata": {},
            "mode": "test",
            "created_on": "2017-07-04T06:06:05.580391Z"
        }
EOD;

        $json = json_decode($str, true);
        $subscription = Subscription::getSchema()->parse($json, [$this->getClient()->getStoreBasedContext()]);
        $this->assertEquals('11111111-1111-1111-1111-111111111111', $subscription->id);
        $this->assertEquals('22222222-2222-2222-2222-222222222222', $subscription->storeId);
        $this->assertEquals('33333333-3333-3333-3333-333333333333', $subscription->transactionTokenId);
        $this->assertEquals(Money::JPY(1000), $subscription->amount);
        $this->assertEquals(1000, $subscription->amountFormatted);
        $this->assertEquals(new Currency('JPY'), $subscription->currency);
        $this->assertEquals(new DateInterval('P15D'), $subscription->cyclicalPeriod);
        $this->assertEquals(Money::JPY(100), $subscription->initialAmount);
        $this->assertEquals(100, $subscription->initialAmountFormatted);
        $this->assertEquals(date_create('2017-07-31'), $subscription->scheduleSettings->startOn);
        $this->assertEquals(new DateTimeZone('Asia/Tokyo'), $subscription->scheduleSettings->zoneId);
        $this->assertTrue($subscription->scheduleSettings->preserveEndOfMonth);
        $this->assertEquals(new DateInterval('P5D'), $subscription->scheduleSettings->retryInterval);
        $this->assertEquals(SubscriptionStatus::CANCELED(), $subscription->status);
        $this->assertEquals(AppTokenMode::TEST(), $subscription->mode);
        $this->assertInstanceOf(ScheduledPayment::class, $subscription->nextPayment);
        $this->assertEquals(date_create('2017-07-04T06:06:05.580391Z'), $subscription->createdOn);
        $this->assertEquals(SubscriptionPlanType::FIXED_CYCLE_AMOUNT(), $subscription->subscriptionPlan->planType);
        $this->assertEquals(Money::JPY(1000), $subscription->subscriptionPlan->fixedCycleAmount);
        $this->assertEquals('9', $subscription->paymentsLeft);
        $this->assertEquals(Money::JPY(5000), $subscription->amountLeft);
        $this->assertEquals(5000, $subscription->amountLeftFormatted);
        $this->assertTrue($subscription->firstChargeAuthorizationOnly);
        $this->assertEquals(new DateInterval('PT5H'), $subscription->firstChargeCaptureAfter);
    }

    public function testCreateSubscription()
    {
        $subscription = $this->createValidSubscription();
        $this->assertEquals(Money::JPY(10000), $subscription->amount);
        $this->assertEquals(new Currency('JPY'), $subscription->currency);
        $this->assertEquals(Period::BIWEEKLY(), $subscription->period);
        $this->assertEquals(Money::JPY(1000), $subscription->initialAmount);
        $this->assertInstanceOf(DateTime::class, $subscription->createdOn);
    }

    public function testCreateSubscriptionWithRecurring()
    {
        $subscription = $this->createValidSubscription(null, null, TokenType::RECURRING());
        $this->assertEquals(Money::JPY(10000), $subscription->amount);
        $this->assertEquals(new Currency('JPY'), $subscription->currency);
        $this->assertEquals(Period::BIWEEKLY(), $subscription->period);
        $this->assertEquals(Money::JPY(1000), $subscription->initialAmount);
        $this->assertInstanceOf(DateTime::class, $subscription->createdOn);
    }

    public function testCreateSubcriptionWithThreeDS()
    {
        $subscription = $this->createValidSubscription(
            null,
            null,
            TokenType::SUBSCRIPTION(),
            new PaymentThreeDS(
                'https://example.com/success',
                null,
                ThreeDSMode::REQUIRE()
            )
        );
        $this->assertEquals(SubscriptionStatus::UNVERIFIED(), $subscription->status);
        $this->assertEquals(ThreeDSMode::REQUIRE(), $subscription->threeDS->mode);
        $this->assertEquals('https://example.com/success', $subscription->threeDS->redirectEndpoint);
        $this->assertNotNull($subscription->threeDS->redirectId);

        $charge = $this->client->getLatestChargeForSubscription(
            $subscription->storeId,
            $subscription->id
        )->awaitResult(5);

        // Confirm 3DS Issuer Token
        $threeDSIssuerToken = $charge->threeDSIssuerToken();
        $this->assertEquals(CallMethod::HTTP_POST(), $threeDSIssuerToken->callMethod);
        $this->assertNotNull($threeDSIssuerToken->contentType);
        $this->assertIsString($threeDSIssuerToken->issuerToken);
        $this->assertNotNull($threeDSIssuerToken->payload);
        $this->assertEquals(PaymentType::CARD(), $threeDSIssuerToken->paymentType);
    }

    public function testCreateSubcriptionWithThreeDSMPI()
    {
        $subscription = $this->createValidSubscription(
            null,
            null,
            TokenType::SUBSCRIPTION(),
            new PaymentThreeDS(
                null,
                null,
                null,
                new ThreeDSMPI(
                    '1234567890123456789012345678',
                    '12',
                    '058e4f09-37c7-47e5-9d24-47e8ffa77442',
                    '7307b449-375a-4297-94d9-81314d4371c2',
                    '2.1.0',
                    'Y'
                )
            )
        )->awaitResult(5);
        $this->assertEquals(ThreeDSMode::PROVIDED(), $subscription->threeDS->mode);
        $this->assertNull($subscription->threeDS->redirectEndpoint);
        $this->assertNull($subscription->threeDS->redirectId);
    }

    public function testCreateAuthorizedSubscription()
    {
        $subscription = $this->createValidSubscription(true, new DateInterval('PT6H'));
        $this->assertEquals(Money::JPY(10000), $subscription->amount);
        $this->assertEquals(new Currency('JPY'), $subscription->currency);
        $this->assertEquals(Period::BIWEEKLY(), $subscription->period);
        $this->assertEquals(Money::JPY(1000), $subscription->initialAmount);
        $this->assertInstanceOf(DateTime::class, $subscription->createdOn);
        $this->assertTrue($subscription->firstChargeAuthorizationOnly);
        $this->assertEquals(new DateInterval('PT6H'), $subscription->firstChargeCaptureAfter);
        $this->assertEquals(SubscriptionStatus::AUTHORIZED(), $subscription->status);
    }

    public function testCreateScheduleSubscription()
    {
        $subscription = $this->createValidScheduleSubscription();
        $this->assertEquals(
            date_create('last day of next month midnight')->setTimezone(new DateTimeZone('Asia/Tokyo')),
            $subscription->scheduleSettings->startOn
        );
        $this->assertEquals(new DateTimeZone('Asia/Tokyo'), $subscription->scheduleSettings->zoneId);
        $this->assertTrue($subscription->scheduleSettings->preserveEndOfMonth);
    }

    public function testCreateCyclicalPeriodWithRetryIntervalSubscription()
    {
        $subscription = $this->createValidCyclicalPeriodSubscription(
            null,
            new DateInterval('P15D'),
            null,
            new ScheduleSettings(null, null, false, new DateInterval('P5D'))
        );
        $this->assertEquals(Money::JPY(10000), $subscription->amount);
        $this->assertEquals(new Currency('JPY'), $subscription->currency);
        $this->assertNull($subscription->period);
        $this->assertEquals(new DateInterval('P15D'), $subscription->cyclicalPeriod);
        $this->assertEquals(new DateInterval('PT120H'), $subscription->scheduleSettings->retryInterval);
        $this->assertEquals(Money::JPY(1000), $subscription->initialAmount);
        $this->assertInstanceOf(DateTime::class, $subscription->createdOn);
    }

    // Bug in test mode installments
    public function testCreateInstallmentSubscription()
    {
        $subscription = $this->createValidInstallmentPlan();
        $this->assertEquals(InstallmentPlanType::FIXED_CYCLES(), $subscription->installmentPlan->planType);
        $this->assertEquals('12', $subscription->installmentPlan->fixedCycles);
        $this->assertEquals(null, $subscription->paymentsLeft);
        $this->assertEquals(Money::JPY(0), $subscription->amountLeft);
    }

    public function testCreateFixedAmountSubscriptionPlan()
    {
        $subscription = $this->createValidFixedAmountSubscriptionPlan();
        $this->assertEquals(SubscriptionPlanType::FIXED_CYCLE_AMOUNT(), $subscription->subscriptionPlan->planType);
        $this->assertEquals(Money::JPY(1000), $subscription->subscriptionPlan->fixedCycleAmount);
    }

    public function testGetSubscription()
    {
        $subscription = $this->createValidSubscription();

        $getSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $this->assertEquals(Money::JPY(10000), $getSubscription->amount);
        $this->assertEquals(new Currency('JPY'), $getSubscription->currency);
        $this->assertEquals(Period::BIWEEKLY(), $getSubscription->period);
        $this->assertEquals(Money::JPY(1000), $getSubscription->initialAmount);
        $this->assertInstanceOf(ScheduledPayment::class, $subscription->nextPayment);
    }

    public function testPatchUnconfirmedSubscription()
    {
        $subscription = $this->createUnconfirmedSubscription();

        $updatedToken = $this->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION());
        $schedule = new ScheduleSettings(date_create('last day of next month'), new DateTimeZone('UTC'), true);
        
        $patchedSubscription = $subscription->patch(
            $updatedToken->id,
            Money::JPY(2000),
            Period::MONTHLY(),
            $schedule,
            null,
            ['reason' => 'PHP SDK test'],
            new SubscriptionPlan(SubscriptionPlanType::FIXED_CYCLE_AMOUNT(), null, Money::JPY(2000))
        )->awaitResult(5);
        $this->assertEquals(Money::JPY(10000), $patchedSubscription->amount);
        $this->assertEquals(new Currency('JPY'), $patchedSubscription->currency);
        $this->assertEquals(Period::MONTHLY(), $patchedSubscription->period);
        $this->assertEquals(Money::JPY(2000), $patchedSubscription->initialAmount);
        $this->assertEquals(
            date_create('last day of next month midnight'),
            $patchedSubscription->scheduleSettings->startOn
        );
        $this->assertEquals(new DateTimeZone('Asia/Tokyo'), $patchedSubscription->scheduleSettings->zoneId);
        $this->assertTrue($patchedSubscription->scheduleSettings->preserveEndOfMonth);
        $this->assertEquals(
            SubscriptionPlanType::FIXED_CYCLE_AMOUNT(),
            $patchedSubscription->subscriptionPlan->planType
        );
        $this->assertEquals(Money::JPY(2000), $patchedSubscription->subscriptionPlan->fixedCycleAmount);
    }

    public function testPatchSubscriptionStatuses()
    {
        $subscription = $this->createValidSubscription();
        $this->assertEquals(SubscriptionStatus::CURRENT(), $subscription->status);
        
        $suspendedSubscription = $subscription->patch(
            null,
            null,
            null,
            null,
            SubscriptionStatus::SUSPENDED()
        );
        $this->assertEquals(SubscriptionStatus::SUSPENDED(), $suspendedSubscription->status);

        $resumedSubscription = $suspendedSubscription->patch(
            null,
            null,
            null,
            null,
            SubscriptionStatus::UNPAID()
        );
        $this->assertEquals(SubscriptionStatus::UNPAID(), $resumedSubscription->status);

        $this->expectException(UnivapayValidationError::class);
        $resumedSubscription->patch(
            null,
            null,
            null,
            null,
            SubscriptionStatus::CURRENT()
        );
    }
    
    public function testCancelSubscription()
    {
        $subscription = $this->createValidSubscription();

        $getSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $this->assertTrue($getSubscription->cancel());

        $canceledSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $this->assertEquals(SubscriptionStatus::CANCELED(), $canceledSubscription->status);
    }

    public function testListSubscription()
    {
        $this->createValidSubscription();
        $subscriptions = $this->getClient()->listSubscriptions();
        $this->assertGreaterThan(0, count($subscriptions->items));
        $this->assertInstanceOf(Subscription::class, reset($subscriptions->items));
    }

    public function testListPaymentsForSubscription()
    {
        $subscription = $this->createValidInstallmentPlan();
        $getSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $payments = $getSubscription->listScheduledPayments();
        $this->assertInstanceOf(Paginated::class, $payments);
        $this->assertGreaterThan(0, count($payments->items));
        $this->assertInstanceOf(ScheduledPayment::class, reset($payments->items));
    }

    public function testListChargesForSubscription()
    {
        $subscription = $this->createValidSubscription();
        $getSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $charges = $getSubscription->listCharges();
        $this->assertInstanceOf(Paginated::class, $charges);
    }

    public function testCreateSubscriptionPlanSimulation()
    {
        $schedule = new ScheduleSettings(date_create('last day of next month'), null, true);
        $subscriptionPlan = new SubscriptionPlan(SubscriptionPlanType::FIXED_CYCLE_AMOUNT(), null, Money::JPY(1000));
        $simulatedPayments = $this->getClient()->createSubscriptionSimulation(
            PaymentType::CARD(),
            Money::JPY(10000),
            Period::MONTHLY(),
            Money::JPY(100),
            $schedule,
            $subscriptionPlan
        );

        $this->assertInstanceOf(SimpleList::class, $simulatedPayments);
        $this->assertEquals(5, count($simulatedPayments->items));
        $this->assertInstanceOf(ScheduledPayment::class, reset($simulatedPayments->items));
    }

    public function testCreateInstallmentPlanSimulation()
    {
        $installmentPlan = new InstallmentPlan(InstallmentPlanType::FIXED_CYCLES(), 12);
        $simulatedPayments = $this->getClient()->createSubscriptionSimulation(
            PaymentType::CARD(),
            Money::JPY(10000),
            Period::MONTHLY(),
            null,
            null,
            null,
            $installmentPlan
        );

        $this->assertInstanceOf(SimpleList::class, $simulatedPayments);
        $this->assertEquals(1, count($simulatedPayments->items));
        $this->assertInstanceOf(ScheduledPayment::class, reset($simulatedPayments->items));
    }
}
