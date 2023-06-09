<?php
namespace UnivapayTest\Integration;

use DateTime;
use DateTimeZone;
use Univapay\Resources\Charge;
use Univapay\Resources\Paginated;
use Univapay\Resources\Subscription;
use Univapay\Resources\Subscription\ScheduledPayment;
use Univapay\Resources\Subscription\ScheduleSettings;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class ScheduledPaymentTest extends TestCase
{
    use IntegrationSuite;

    public function testScheduledPaymentParse()
    {
        $str = <<<EOD
        {
            "id": "11e8960f-dd31-28ca-a8a8-ab5fd4c72b70",
            "due_date": "2019-05-02",
            "zone_id": "Asia/Tokyo",
            "amount": 560,
            "currency": "JPY",
            "amount_formatted": 560,
            "is_paid": false,
            "is_last_payment": true,
            "created_on": "2018-08-02T04:52:31.250338Z"
        }
EOD;
        $json = json_decode($str, true);
        $payment = ScheduledPayment::getSchema()->parse($json, [$this->getClient()->getStoreBasedContext()]);

        $this->assertEquals('11e8960f-dd31-28ca-a8a8-ab5fd4c72b70', $payment->id);
        $this->assertEquals(date_create('2019-05-02'), $payment->dueDate);
        $this->assertEquals(new DateTimeZone('Asia/Tokyo'), $payment->zoneId);
        $this->assertEquals(Money::JPY(560), $payment->amount);
        $this->assertEquals(560, $payment->amountFormatted);
        $this->assertFalse($payment->isPaid);
        $this->assertTrue($payment->isLastPayment);
        $this->assertEquals(date_create('2018-08-02T04:52:31.250338Z'), $payment->createdOn);
    }

    public function testListChargesForScheduledPayment()
    {
        $subscription = $this->createValidInstallmentPlan();
        $getSubscription = $this->getClient()->getSubscription($this->storeAppJWT->storeId, $subscription->id);
        $payments = $getSubscription->listScheduledPayments();
        $charges = end($payments->items)->listCharges();
        
        $this->assertInstanceOf(Paginated::class, $charges);
        $this->assertGreaterThan(0, $charges->items);
        $this->assertInstanceOf(Charge::class, reset($charges->items));
    }
}
