<?php
namespace UnivapayTest\Integration;

use DateTime;
use Univapay\Enums\CancelStatus;
use Univapay\Enums\ChargeStatus;
use Univapay\Enums\PaymentType;
use Univapay\Errors\UnivapayLogicError;
use Univapay\Errors\UnivapayRequestError;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class ChargeTest extends TestCase
{
    use IntegrationSuite;

    public function testCreateCharge()
    {
        $charge = $this->createValidCharge(true);
        $this->assertEquals(Money::JPY(1000), $charge->requestedAmount);
        $this->assertEquals(new Currency('JPY'), $charge->requestedCurrency);
        $this->assertInstanceOf(DateTime::class, $charge->createdOn);
    }

    public function testCreateChargeOnToken()
    {
        $charge = $this->createValidToken()->createCharge(Money::JPY(1000));
        $this->assertEquals(Money::JPY(1000), $charge->requestedAmount);
        $this->assertEquals(new Currency('JPY'), $charge->requestedCurrency);
    }

    public function testAuthCaptureCharge()
    {
        $charge = $this->createValidCharge(false);
        $captured = $charge->capture(Money::JPY(1000));
        $this->assertTrue($captured);
    }

    public function testPartialAuthCaptureCharge()
    {
        $charge = $this->createValidCharge(false);
        $captured = $charge->capture(Money::JPY(500));
        $this->assertTrue($captured);
    }

    public function testDefaultAuthCaptureCharge()
    {
        $charge = $this->createValidCharge(false);
        $captured = $charge->capture();
        $this->assertTrue($captured);
    }

    public function testCaptureAtCharge()
    {
        $captureAt = date_create('+1 day midnight');
        $charge = $this->createValidCharge(false, $captureAt);
        $this->assertEquals(ChargeStatus::AUTHORIZED(), $charge->status);
        $this->assertEquals($captureAt, $charge->captureAt);
    }

    public function testTooLongCaptureAtCharge()
    {
        $this->expectException(UnivapayLogicError::class);
        $captureAt = date_create('+14 day');
        $charge = $this->createValidCharge(false, $captureAt);
    }

    public function testTooShortCaptureAtCharge()
    {
        $this->expectException(UnivapayLogicError::class);
        $captureAt = date_create('+30 minutes');
        $charge = $this->createValidCharge(false, $captureAt);
    }

    public function testOnlyDirectCurrencyCharge()
    {
        $charge = $this->createValidCharge(null, null, true);
        $this->assertTrue($charge->onlyDirectCurrency);
    }

    public function testPatchCharge()
    {
        $charge = $this->createValidCharge(true);
        $this->assertEquals(0, count($charge->metadata));
        
        $charge = $charge->patch(['testId' => 12345]);
        $this->assertTrue($charge->metadata['testId'] === 12345);
    }

    public function testInvalidCharge()
    {
        $this->expectException(UnivapayRequestError::class);
        $transactionToken = $this->createValidToken();
        $this->getClient()->createCharge($transactionToken->id, Money::JPY(-1000));
    }

    public function testInvalidAuthCapture()
    {
        $this->expectException(UnivapayRequestError::class);
        $charge = $this->createValidCharge(false);
        $charge->capture(Money::JPY(2000));
    }
    
    public function testCancelAuthCharge()
    {
        $charge = $this->createValidCharge(false);
        $this->assertEquals(ChargeStatus::AUTHORIZED(), $charge->status);
        $cancel = $charge->cancel(['something'=>'anything'])->awaitResult(5);
        $this->assertEquals(CancelStatus::SUCCESSFUL(), $cancel->status);
        $this->assertEquals($cancel->metadata['something'], 'anything');
    }
    
    public function testInvalidCancel()
    {
        $charge = $this->createValidCharge();
        $this->assertEquals(ChargeStatus::SUCCESSFUL(), $charge->status);
        
        $this->expectException(UnivapayRequestError::class);
        $charge->cancel();
    }

    public function testCreateQrMerchantCharge()
    {
        $charge = $this->createValidCharge(null, null, null, PaymentType::QR_MERCHANT());
        $this->assertEquals(ChargeStatus::AWAITING(), $charge->status);
        
        $qrToken = $charge->qrMerchantToken();
        $this->assertTrue($qrToken->ready);
        $this->assertNotNull($qrToken->qrImageUrl);
    }

    public function testCreateOnlineCharge()
    {
        $charge = $this->createValidCharge(null, null, null, PaymentType::ONLINE());
        $this->assertEquals(ChargeStatus::AWAITING(), $charge->status);
        
        $onlineToken = $charge->onlineToken();
        $this->assertNotNull($onlineToken->issuerToken);
        $this->assertNotNull($onlineToken->callMethod);
    }
}
