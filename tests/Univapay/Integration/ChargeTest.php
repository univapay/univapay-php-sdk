<?php
namespace UnivapayTest\Integration;

use DateTime;
use Univapay\Enums\AppTokenMode;
use Univapay\Enums\CancelStatus;
use Univapay\Enums\ChargeStatus;
use Univapay\Enums\PaymentType;
use Univapay\Enums\TokenType;
use Univapay\Errors\UnivapayLogicError;
use Univapay\Errors\UnivapayRequestError;
use Univapay\Resources\Charge;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class ChargeTest extends TestCase
{
    use IntegrationSuite;

    public function testParseChargeWithError()
    {
        $str = <<<EOD
        {
            "id": "11ed0cce-59e5-795a-b95c-fb1234567890",
            "store_id": "11e99edf-6075-c71c-b6d5-ef1237890",
            "transaction_token_id": "11ed0cce-589a-5584-b959-631234567890",
            "transaction_token_type": "one_time",
            "subscription_id": "12ed0cce-59e5-795a-b95c-fb1234567890",
            "requested_amount": 100,
            "requested_currency": "JPY",
            "requested_amount_formatted": 100,
            "charged_amount": 100,
            "charged_currency": "JPY",
            "charged_amount_formatted": 100,
            "only_direct_currency": false,
            "capture_at": "2022-07-26T10:33:16.308043Z",
            "status": "failed",
            "error": {
              "code": 301,
              "message": "The card number is not valid"
            },
            "metadata": {},
            "mode": "live",
            "created_on": "2022-07-26T10:33:12.934225Z",
            "merchant_id": "11e99ede-ccb4-dfcc-beea-3b1234567890"
        }
EOD;
        $json = json_decode($str, $assoc = true);
        $charge = Charge::getSchema()->parse($json, [$this->getClient()->getStoreBasedContext()]);
        $this->assertEquals('11ed0cce-59e5-795a-b95c-fb1234567890', $charge->id);
        $this->assertEquals('11e99edf-6075-c71c-b6d5-ef1237890', $charge->storeId);
        $this->assertEquals('11ed0cce-589a-5584-b959-631234567890', $charge->transactionTokenId);
        $this->assertEquals('12ed0cce-59e5-795a-b95c-fb1234567890', $charge->subscriptionId);
        $this->assertEquals(TokenType::ONE_TIME(), $charge->transactionTokenType);
        $this->assertEquals(Money::JPY(100), $charge->requestedAmount);
        $this->assertEquals(new Currency('JPY'), $charge->requestedCurrency);
        $this->assertEquals(Money::JPY(100), $charge->chargedAmount);
        $this->assertEquals(new Currency('JPY'), $charge->chargedCurrency);
        $this->assertEquals(false, $charge->onlyDirectCurrency);
        $this->assertEquals(date_create('2022-07-26T10:33:16.308043Z'), $charge->captureAt);
        $this->assertEquals(date_create('2022-07-26T10:33:12.934225Z'), $charge->createdOn);
        $this->assertEquals(301, $charge->error['code']);
        $this->assertEquals('The card number is not valid', $charge->error['message']);
        $this->assertEquals(ChargeStatus::FAILED(), $charge->status);
        $this->assertEquals(AppTokenMode::LIVE(), $charge->mode);
    }

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
        $cancel = $charge->cancel(['something'=>'anything'])->awaitResult();
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
