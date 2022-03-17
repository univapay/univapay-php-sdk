<?php
namespace UnivapayTest\Integration;

use DateInterval;
use DateTime;
use DateTimeZone;
use Univapay\Enums\AppTokenMode;
use Univapay\Enums\InstallmentPlanType;
use Univapay\Enums\PaymentType;
use Univapay\Enums\Period;
use Univapay\Enums\SubscriptionStatus;
use Univapay\Enums\TokenType;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Resources\Paginated;
use Univapay\Resources\SimpleList;
use Univapay\Resources\Subscription;
use Univapay\Resources\Subscription\InstallmentPlan;
use Univapay\Resources\Subscription\ScheduledPayment;
use Univapay\Resources\Subscription\ScheduleSettings;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    use IntegrationSuite;

    public function testSubscriptionParse()
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
                "preserve_end_of_month": true
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
            "installment_plan": {
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
        $this->assertEquals(SubscriptionStatus::CANCELED(), $subscription->status);
        $this->assertEquals(AppTokenMode::TEST(), $subscription->mode);
        $this->assertInstanceOf(ScheduledPayment::class, $subscription->nextPayment);
        $this->assertEquals(date_create('2017-07-04T06:06:05.580391Z'), $subscription->createdOn);
        $this->assertEquals(InstallmentPlanType::FIXED_CYCLE_AMOUNT(), $subscription->installmentPlan->planType);
        $this->assertEquals(Money::JPY(1000), $subscription->installmentPlan->fixedCycleAmount);
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

    // Bug in test mode installments
    public function testCreateInstallmentSubscription()
    {
        $this->markTestSkipped("Bug in test mode installments");
        $subscription = $this->createValidInstallmentSubscription();
        $this->assertEquals(InstallmentPlanType::FIXED_CYCLES(), $subscription->installmentPlan->planType);
        $this->assertEquals('10', $subscription->installmentPlan->fixedCycles);
        $this->assertEquals('9', $subscription->paymentsLeft);
        $this->assertInstanceOf(Money::class, $subscription->amountLeft);
        $this->assertInstanceOf(ScheduledPayment::class, $subscription->nextPayment);
    }

    public function testCreateFixedAmountInstallmentSubscription()
    {
        $subscription = $this->createValidFixedAmountInstallmentSubscription();
        $this->assertEquals(InstallmentPlanType::FIXED_CYCLE_AMOUNT(), $subscription->installmentPlan->planType);
        $this->assertEquals(Money::JPY(1000), $subscription->installmentPlan->fixedCycleAmount);
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
            new InstallmentPlan(InstallmentPlanType::FIXED_CYCLE_AMOUNT(), null, Money::JPY(2000))
        )->awaitResult();
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
            InstallmentPlanType::FIXED_CYCLE_AMOUNT(),
            $patchedSubscription->installmentPlan->planType
        );
        $this->assertEquals(Money::JPY(2000), $patchedSubscription->installmentPlan->fixedCycleAmount);
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
        $subscription = $this->createValidInstallmentSubscription();
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

    public function testCreateSubscriptionSimulation()
    {
        $schedule = new ScheduleSettings(date_create('last day of next month'), null, true);
        $installmentPlan = new InstallmentPlan(InstallmentPlanType::FIXED_CYCLE_AMOUNT(), null, Money::JPY(1000));
        $simulatedPayments = $this->getClient()->createSubscriptionSimulation(
            PaymentType::CARD(),
            Money::JPY(10000),
            Period::MONTHLY(),
            Money::JPY(100),
            $schedule,
            $installmentPlan
        );

        $this->assertInstanceOf(SimpleList::class, $simulatedPayments);
        $this->assertEquals(5, count($simulatedPayments->items));
        $this->assertInstanceOf(ScheduledPayment::class, reset($simulatedPayments->items));
    }
}
