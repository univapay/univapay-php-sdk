<?php
namespace UnivapayTest\Integration;

use Univapay\UnivapayClient;
use Univapay\UnivapayClientOptions;
use Univapay\Enums\PaymentType;
use Univapay\Enums\TokenType;
use Univapay\Errors\UnivapayResourceConflictError;
use Univapay\Requests\Handlers\BasicRetryHandler;
use PHPUnit\Framework\TestCase;

class BasicRetryHandlerTest extends TestCase
{
    use IntegrationSuite;
    
    private $rateLimitHandledClient;
    private $rateLimitUnhandledClient;

    public function tearDown()
    {
        sleep(1); // Sleep to let the API register the subscription token previously created so it can be deleted
        $this->deactivateExistingSubscriptionToken();
        $this->getClient()->setHandlers();
    }

    public function testIsAbleToGetConflictError()
    {
        $this->expectException(UnivapayResourceConflictError::class);
        $this->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION());
        $this->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION());
    }

    /**
     * @depends testIsAbleToGetConflictError
     */
    public function testIsAbleToRetryBeforeGivingUp()
    {
        $this->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION());
        $this->getClient()->addHandlers(new BasicRetryHandler(
            UnivapayResourceConflictError::class,
            2,
            2,
            function (UnivapayResourceConflictError $error) {
                return $error->code === 'NON_UNIQUE_ACTIVE_TOKEN';
            }
        ));
        $shouldTakeAsLong = date_create('+4 seconds');
        try {
            $this->createValidToken(PaymentType::CARD(), TokenType::SUBSCRIPTION());
        } catch (UnivapayResourceConflictError $error) {
            $finish = date_create();
            $this->assertEquals('NON_UNIQUE_ACTIVE_TOKEN', $error->code);
            $this->assertTrue($finish > $shouldTakeAsLong);
        }
    }
}
