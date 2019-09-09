<?php
namespace UnivapayTest\Integration;

use DateInterval;
use Univapay\Enums\ActiveFilter;
use Univapay\Enums\AppTokenMode;
use Univapay\Enums\ConvenienceStore;
use Univapay\Enums\Gateway;
use Univapay\Enums\PaymentType;
use Univapay\Enums\QRBrand;
use Univapay\Enums\TokenType;
use Univapay\Errors\UnivapayRequestError;
use Univapay\Resources\PaymentData\PhoneNumber;
use Univapay\Resources\PaymentMethod\CardPayment;
use Univapay\Resources\PaymentMethod\CardPaymentPatch;
use Univapay\Resources\PaymentMethod\PaymentMethodPatch;
use PHPUnit\Framework\TestCase;

class TransactionTokenTest extends TestCase
{
    use IntegrationSuite;

    public function testCreateToken()
    {
        $transactionToken = $this->createValidToken();
        $this->assertEquals('test@test.com', $transactionToken->email);
        $this->assertEquals(TokenType::ONE_TIME(), $transactionToken->type);
        $this->assertEquals(PaymentType::CARD(), $transactionToken->paymentType);
        $this->assertNull($transactionToken->confirmed);
        $this->assertEquals('PHP TEST', $transactionToken->metadata['customer_id']);
        $this->assertEquals('PHP TEST', $transactionToken->data->card->cardholder);
        $this->assertEquals('02', $transactionToken->data->card->expMonth);
        $this->assertEquals('2022', $transactionToken->data->card->expYear);
        $this->assertEquals('test line 1', $transactionToken->data->billing->line1);
        $this->assertEquals('test line 2', $transactionToken->data->billing->line2);
        $this->assertEquals('test state', $transactionToken->data->billing->state);
        $this->assertEquals('test city', $transactionToken->data->billing->city);
        $this->assertEquals('JP', $transactionToken->data->billing->country);
        $this->assertEquals('101-1111', $transactionToken->data->billing->zip);
        $this->assertEquals(PhoneNumber::JP, $transactionToken->data->billing->phoneNumber->countryCode);
        $this->assertEquals('12910298309128', $transactionToken->data->billing->phoneNumber->localNumber);
    }

    public function testCreateApplePayToken()
    {
        $this->markTestIncomplete('Missing Apple Pay Certificate for Merchant');
        $transactionToken = $this->createValidToken(PaymentType::APPLE_PAY());
        $this->assertEquals('test@test.com', $transactionToken->email);
        $this->assertEquals(TokenType::ONE_TIME(), $transactionToken->type);
        $this->assertEquals(PaymentType::APPLE_PAY(), $transactionToken->paymentType);
        $this->assertNull($transactionToken->confirmed);
        $this->assertEquals('PHP TEST', $transactionToken->metadata['customer_id']);
        $this->assertEquals('PHP TEST', $transactionToken->data->card->cardholder);
        $this->assertEquals('test line 1', $transactionToken->data->billing->line1);
        $this->assertEquals('test line 2', $transactionToken->data->billing->line2);
        $this->assertEquals('test state', $transactionToken->data->billing->state);
        $this->assertEquals('test city', $transactionToken->data->billing->city);
        $this->assertEquals('JP', $transactionToken->data->billing->country);
        $this->assertEquals('101-1111', $transactionToken->data->billing->zip);
        $this->assertEquals(PhoneNumber::JP, $transactionToken->data->billing->phoneNumber->countryCode);
        $this->assertEquals('12910298309128', $transactionToken->data->billing->phoneNumber->localNumber);
    }

    public function testCreateKonbiniToken()
    {
        $transactionToken = $this->createValidToken(PaymentType::KONBINI());
        $this->assertEquals('test@test.com', $transactionToken->email);
        $this->assertEquals(TokenType::ONE_TIME(), $transactionToken->type);
        $this->assertEquals(PaymentType::KONBINI(), $transactionToken->paymentType);
        $this->assertNull($transactionToken->confirmed);
        $this->assertEquals('PHP TEST', $transactionToken->metadata['customer_id']);
        $this->assertEquals('PHP test', $transactionToken->data->customerName);
        $this->assertEquals(PhoneNumber::JP, $transactionToken->data->phoneNumber->countryCode);
        $this->assertEquals('12910298309128', $transactionToken->data->phoneNumber->localNumber);
        $this->assertEquals(ConvenienceStore::SEVEN_ELEVEN(), $transactionToken->data->convenienceStore);
        $this->assertEquals(new DateInterval('P7D'), $transactionToken->data->expirationPeriod);
    }

    public function testCreateQRScanToken()
    {
        $transactionToken = $this->createValidToken(PaymentType::QR_SCAN());
        $this->assertEquals('test@test.com', $transactionToken->email);
        $this->assertEquals(PaymentType::QR_SCAN(), $transactionToken->paymentType);
        $this->assertNull($transactionToken->confirmed);
        $this->assertEquals(Gateway::ORIGAMI(), $transactionToken->data->gateway);
        $this->assertEquals(QRBrand::ORIGAMI(), $transactionToken->data->brand);
        $this->assertEquals('PHP TEST', $transactionToken->metadata['customer_id']);
    }

    public function testCreatePaidyToken()
    {
        $transactionToken = $this->createValidToken(PaymentType::PAIDY(), TokenType::RECURRING());
        $this->assertEquals('test@test.com', $transactionToken->email);
        $this->assertEquals(PaymentType::PAIDY(), $transactionToken->paymentType);
        $this->assertTrue($transactionToken->confirmed);
        $this->assertEquals(TokenType::RECURRING(), $transactionToken->type);
        $this->assertEquals(PhoneNumber::JP, $transactionToken->data->phoneNumber->countryCode);
        $this->assertEquals('08012345678', $transactionToken->data->phoneNumber->localNumber);
        $this->assertEquals('Address Line 1', $transactionToken->data->shippingAddress->line1);
        $this->assertEquals('Address Line 2', $transactionToken->data->shippingAddress->line2);
        $this->assertEquals('State', $transactionToken->data->shippingAddress->state);
        $this->assertEquals('City', $transactionToken->data->shippingAddress->city);
        $this->assertEquals('1001000', $transactionToken->data->shippingAddress->zip);
        $this->assertEquals('PHP TEST', $transactionToken->metadata['customer_id']);
    }

    public function testGetExistingToken()
    {
        $transactionToken = $this->createValidToken();
        $retrievedTransactionToken = $this->getClient()->getTransactionToken(
            $transactionToken->id
        );
        $this->assertEquals($transactionToken->id, $retrievedTransactionToken->id);
    }

    public function testListExistingTokens()
    {
        $localCustomerId = substr(sha1(rand()), 0, 15);
        $transactionToken = $this->getClient()->createToken(
            $this->createCardPayment(TokenType::RECURRING()),
            $localCustomerId
        );

        $this->assertTrue(isset($transactionToken->metadata['gopay-customer-id']));
        
        $maxRetries = 3;
        $tokenList = null;
        do {
            $maxRetries--;
            sleep(1); // It takes a bit of time for to index to get updated
            $tokenList = $this->getClient()->listTransactionTokens(
                'test@test.com',
                $transactionToken->metadata['gopay-customer-id'],
                TokenType::RECURRING(),
                AppTokenMode::TEST(),
                ActiveFilter::ACTIVE()
            );
        } while (empty($tokenList->items) && $maxRetries > 0);
        
        $this->assertTrue(count($tokenList->items) === 1);
        $this->assertTrue(array_key_exists('gopay-customer-id', $tokenList->items[0]->metadata));
    }

    public function testPatchExistingToken()
    {
        $transactionToken = $this->createValidToken();
        $this->assertEquals('test@test.com', $transactionToken->email);
        $this->assertEquals('PHP TEST', $transactionToken->metadata['customer_id']);
        
        $patchRequest = new PaymentMethodPatch(
            'test@changed.int',
            ['customer_id' => 'PHP TESTER']
        );
        $patchedTxToken = $transactionToken->patch($patchRequest);
        $this->assertEquals('test@changed.int', $patchedTxToken->email);
        $this->assertEquals('PHP TESTER', $patchedTxToken->metadata['customer_id']);
        $this->assertTrue($patchedTxToken->data !== null);
    }

    public function testPatchExistingCardPayment()
    {
        $transactionToken = $this->createValidToken();
        $this->assertEquals('test@test.com', $transactionToken->email);
        $this->assertEquals('PHP TEST', $transactionToken->metadata['customer_id']);
        
        $patchRequest = new CardPaymentPatch(
            999,
            'test@changed.int',
            null
        );
        $patchedTxToken = $transactionToken->patch($patchRequest);
        $this->assertEquals('test@changed.int', $patchedTxToken->email);
        $this->assertEquals('PHP TEST', $patchedTxToken->metadata['customer_id']);
    }

    public function testDeleteExistingToken()
    {
        $transactionToken = $this->createValidToken();
        $transactionToken->deactivate();

        $deactivatedTransactionToken = $this->getClient()->getTransactionToken($transactionToken->id);
        $this->assertFalse($deactivatedTransactionToken->active);
    }

    public function testInvalidCardNumber()
    {
        $this->expectException(UnivapayRequestError::class);
        $this->getClient()->createToken($this->createCardPayment(TokenType::ONE_TIME(), '4242424242424243'));
    }
}
