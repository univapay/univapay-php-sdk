<?php
namespace UnivapayTest\Integration;

use DateTime;
use Univapay\Enums\AppTokenMode;
use Univapay\Enums\CancelStatus;
use Univapay\Enums\CallMethod;
use Univapay\Enums\ChargeStatus;
use Univapay\Enums\PaymentType;
use Univapay\Enums\ThreeDSMode;
use Univapay\Enums\TokenType;
use Univapay\Errors\UnivapayRequestError;
use Univapay\Resources\Charge;
use Univapay\Resources\PaymentThreeDS;
use Univapay\Resources\Redirect;
use Univapay\Resources\ThreeDSMPI;
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
            "redirect": {
              "endpoint": "https://test.int/endpoint?foo=bar",
              "redirect_id": "11ed0cce-59e5-795a-b95c-rd1234567890"
            },
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
        $this->assertEquals('https://test.int/endpoint?foo=bar', $charge->redirect->endpoint);
        $this->assertEquals('11ed0cce-59e5-795a-b95c-rd1234567890', $charge->redirect->redirectId);
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

    public function testCreateChargeWithRedirect()
    {
        $charge = $this->createValidToken()->createCharge(
            Money::JPY(1000),
            true,
            null,
            null,
            null,
            new Redirect("https://test.int/endpoint?foo=bar")
        );
        $this->assertEquals(Money::JPY(1000), $charge->requestedAmount);
        $this->assertEquals(new Currency('JPY'), $charge->requestedCurrency);
        $this->assertEquals('https://test.int/endpoint?foo=bar', $charge->redirect->endpoint);
        $this->assertNotNull($charge->redirect->redirectId);
    }

    public function testCreateChargeWithThreeDS()
    {
        $charge = $this->createValidToken()->createCharge(
            Money::JPY(100),
            true,
            null,
            null,
            null,
            null,
            new PaymentThreeDS(
                "https://test.int/endpoint?foo=bar",
                ThreeDSMode::REQUIRE()
            )
        )->awaitResult(5);
        $this->assertEquals(Money::JPY(100), $charge->requestedAmount);

        // Confirm 3DS Issuer Token
        $threeDSIssuerToken = $charge->threeDSIssuerToken();
        $this->assertEquals(CallMethod::HTTP_POST(), $threeDSIssuerToken->callMethod);
        $this->assertNotNull($threeDSIssuerToken->contentType);
        $this->assertIsString($threeDSIssuerToken->issuerToken);
        $this->assertNotNull($threeDSIssuerToken->payload);
        $this->assertEquals(PaymentType::CARD(), $threeDSIssuerToken->paymentType);
    }

    public function testCreateChargeWithThreeDSMPI()
    {
        $charge = $this->createValidToken()->createCharge(
            Money::JPY(100),
            true,
            null,
            null,
            null,
            null,
            new PaymentThreeDS(
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
        $this->assertEquals(Money::JPY(100), $charge->requestedAmount);
        $this->assertEquals(ChargeStatus::SUCCESSFUL(), $charge->status);
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
        $this->expectException(UnivapayRequestError::class);
        $captureAt = date_create('+14 day');
        $charge = $this->createValidCharge(false, $captureAt);
    }

    public function testTooShortCaptureAtCharge()
    {
        $this->expectException(UnivapayRequestError::class);
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
        $postCancelCharge = $charge->awaitResult(5);
        $this->assertEquals(ChargeStatus::CANCELED(), $postCancelCharge->status);
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
