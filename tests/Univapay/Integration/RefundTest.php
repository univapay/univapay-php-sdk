<?php
namespace UnivapayTest\Integration;

use Univapay\Enums\RefundReason;
use Univapay\Errors\UnivapayRequestError;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class RefundTest extends TestCase
{
    use IntegrationSuite;

    public function testCreateRefund()
    {
        $refund = $this->createValidRefund();
        $this->assertEquals(new Currency('JPY'), $refund->currency);
        $this->assertEquals(Money::JPY(1000), $refund->amount);
        $this->assertEquals(RefundReason::FRAUD(), $refund->reason);
        $this->assertEquals('test', $refund->message);
        $this->assertEquals(['something' => 'value'], $refund->metadata);
    }

    public function testInvalidRefund()
    {
        $this->expectException(UnivapayRequestError::class);
        $charge = $this->createValidCharge(true);
        $charge->createRefund(Money::JPY(2000), RefundReason::FRAUD(), 'test', ['something' => 'value']);
    }
}
