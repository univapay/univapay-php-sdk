<?php
namespace UnivapayTest\Integration;

use DateInterval;
use DateTimeZone;
use Univapay\Enums\ActiveFilter;
use Univapay\Enums\AppTokenMode;
use Univapay\Enums\ConvenienceStore;
use Univapay\Enums\InstallmentPlanType;
use Univapay\Enums\PaymentType;
use Univapay\Enums\Period;
use Univapay\Enums\RefundReason;
use Univapay\Enums\TokenType;
use Univapay\Resources\InstallmentPlan;
use Univapay\Resources\ScheduleSettings;
use Univapay\Resources\PaymentData\Address;
use Univapay\Resources\PaymentData\ConvenienceStoreData;
use Univapay\Resources\PaymentData\PhoneNumber;
use Univapay\Resources\PaymentData\PaidyData;
use Univapay\Resources\PaymentMethod\ApplePayPayment;
use Univapay\Resources\PaymentMethod\CardPayment;
use Univapay\Resources\PaymentMethod\ConvenienceStorePayment;
use Univapay\Resources\PaymentMethod\PaidyPayment;
use Univapay\Resources\PaymentMethod\QRScanPayment;
use UnivapayTest\Integration\CardNumber;
use Money\Money;

trait Requests
{
    public static $SUCCESSFUL = '4916741415383284';
    public static $CHARGE_FAIL = '4111111111111111';

    abstract protected function init();
    abstract public function getClient();

    public function createValidToken(
        PaymentType $paymentType = null,
        TokenType $type = null,
        $cardNumber = null
    ) {
        $paymentType = isset($paymentType) ? $paymentType : PaymentType::CARD();
        $type = isset($type) ? $type : TokenType::ONE_TIME();
        $cardNumber = isset($cardNumber) ? $cardNumber : static::$SUCCESSFUL;
        $paymentMethod = null;

        switch ($paymentType) {
            case PaymentType::CARD():
                $paymentMethod = $this->createCardPayment($type, $cardNumber);
                break;
            case PaymentType::APPLE_PAY():
                $paymentMethod = $this->createApplePayPayment($type, $cardNumber);
                break;
            case PaymentType::KONBINI():
                $paymentMethod = $this->createKonbiniPayment($type);
                break;
            case PaymentType::QR_SCAN():
                $paymentMethod = $this->createQRScanPayment();
                break;
            case PaymentType::PAIDY():
                $paymentMethod = $this->createPaidyPayment($type);
        }
        return $this->getClient()->createToken($paymentMethod);
    }

    public function createCardPayment(TokenType $type, $cardNumber = null)
    {
        $cardNumber = isset($cardNumber) ? $cardNumber : static::$SUCCESSFUL;
        return new CardPayment(
            'test@test.com',
            'PHP test',
            $cardNumber,
            '02',
            '2022',
            '123',
            $type,
            null,
            new Address(
                'test line 1',
                'test line 2',
                'test state',
                'test city',
                'jp',
                '101-1111'
            ),
            new PhoneNumber(PhoneNumber::JP, '12910298309128'),
            ['customer_id' => 'PHP TEST']
        );
    }

    public function createApplePayPayment(TokenType $type)
    {
        $applePayToken = getenv('Univapay_PHP_TEST_APPLEPAY_TOKEN');
        if (is_null($applePayToken)) {
            $this->fail('Univapay_PHP_TEST_APPLEPAY_TOKEN not defined!');
        }
        return new ApplePayPayment(
            'test@test.com',
            'PHP test',
            $applePayToken,
            $type,
            null,
            new Address(
                'test line 1',
                'test line 2',
                'test state',
                'test city',
                'jp',
                '101-1111'
            ),
            new PhoneNumber(PhoneNumber::JP, '12910298309128'),
            ['customer_id' => 'PHP TEST']
        );
    }

    public function createKonbiniPayment(TokenType $type)
    {
        return new ConvenienceStorePayment(
            'test@test.com',
            new ConvenienceStoreData(
                'PHP test',
                new PhoneNumber(PhoneNumber::JP, '12910298309128'),
                ConvenienceStore::SEVEN_ELEVEN(),
                new DateInterval('P7D')
            ),
            $type,
            null,
            ['customer_id' => 'PHP TEST']
        );
    }

    public function createQRScanPayment()
    {
        return new QRScanPayment(
            'test@test.com',
            'test-gateway',
            ['customer_id' => 'PHP TEST']
        );
    }

    public function createPaidyPayment(TokenType $type)
    {
        $paidyToken = getenv('Univapay_PHP_TEST_PAIDY_TOKEN');
        if (is_null($paidyToken)) {
            $this->fail('Univapay_PHP_TEST_PAIDY_TOKEN not defined!');
        }
        return new PaidyPayment(
            new PaidyData(
                $paidyToken,
                new Address(
                    'Address Line 1',
                    'Address Line 2',
                    'State',
                    'City',
                    'Country',
                    '1001000'
                ),
                new PhoneNumber(
                    PhoneNumber::JP,
                    '08012345678'
                )
            ),
            'test@test.com',
            $type,
            null,
            ['customer_id' => 'PHP TEST']
        );
    }

    public function createValidCharge($capture = null, $captureAt = null, $onlyDirectCurrency = null)
    {
        $capture = isset($capture) ? $capture : true;
        $transactionToken = $this->createValidToken();
        $charge = $this->getClient()->createCharge(
            $transactionToken->id,
            Money::JPY(1000),
            $capture,
            $captureAt,
            null,
            null,
            $onlyDirectCurrency
        );
        return $charge->awaitResult();
    }

    public function createValidRefund()
    {
        $charge = $this->createValidCharge(true);
        return $charge->createRefund(
            Money::JPY(1000),
            RefundReason::FRAUD(),
            'test',
            ['something' => 'value']
        )->awaitResult();
    }

    public function createValidSubscription()
    {
        $this->deactivateExistingSubscriptionToken();
        return $this
            ->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION())
            ->createSubscription(
                Money::JPY(10000),
                Period::BIWEEKLY(),
                Money::JPY(1000)
            )
            ->awaitResult();
    }

    public function createValidScheduleSubscription()
    {
        $this->deactivateExistingSubscriptionToken();
        $schedule = new ScheduleSettings(
            date_create('last day of next month midnight'),
            new DateTimeZone('Asia/Tokyo'),
            true
        );
        return $this
            ->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION())
            ->createSubscription(
                Money::JPY(10000),
                Period::MONTHLY(),
                Money::JPY(1000),
                $schedule
            )
            ->awaitResult();
    }

    public function createValidInstallmentSubscription()
    {
        $this->deactivateExistingSubscriptionToken();
        $installmentPlan = new InstallmentPlan(
            InstallmentPlanType::FIXED_CYCLES(),
            10
        );
        return $this
            ->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION())
            ->createSubscription(
                Money::JPY(10000),
                Period::BIWEEKLY(),
                Money::JPY(1000),
                null,
                $installmentPlan
            )
            ->awaitResult();
    }
    
    public function createUnconfirmedSubscription()
    {
        $this->deactivateExistingSubscriptionToken();
        return $this
            ->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION(), static::$CHARGE_FAIL)
            ->createSubscription(
                Money::JPY(10000),
                Period::BIWEEKLY(),
                Money::JPY(1000)
            )
            ->awaitResult();
    }

    public function deactivateExistingSubscriptionToken()
    {
        $tokenList = $this->getClient()->listTransactionTokens(
            null,
            null,
            TokenType::SUBSCRIPTION(),
            AppTokenMode::TEST(),
            ActiveFilter::ACTIVE()
        );
        
        foreach ($tokenList->items as $token) {
            $token->deactivate();
        }
    }
}