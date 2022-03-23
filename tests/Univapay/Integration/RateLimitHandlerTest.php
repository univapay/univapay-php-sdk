<?php
namespace UnivapayTest\Integration;

use Univapay\UnivapayClient;
use Univapay\UnivapayClientOptions;
use Univapay\Errors\UnivapayRateLimitedError;
use Univapay\Requests\Handlers\RateLimitHandler;
use PHPUnit\Framework\TestCase;

class RateLimitHandlerTest extends TestCase
{
    use IntegrationSuite;
    
    private $rateLimitHandledClient;
    private $rateLimitUnhandledClient;

    public function setUp(): void
    {
        if (is_null($this->storeAppJWT)) {
            $this->init();
        }
        $clientOptions = new UnivapayClientOptions($this->clientOptions->endpoint);
        $clientOptions->rateLimitHandler = new RateLimitHandler(0);
        $this->rateLimitUnhandledClient = new UnivapayClient($this->storeAppJWT, $clientOptions);
        $this->rateLimitHandledClient = $this->getClient();
    }

    public function testIsAbleToHitRateLimiterWhenUnhandled()
    {
        $this->expectException(UnivapayRateLimitedError::class);
        $testLength = date_create('+5 seconds');
        while (date_create() < $testLength) {
            $this->rateLimitUnhandledClient->getCheckoutInfo();
        }

        // Usually unable to hit it on CI, so just skip. Just test locally.
        $this->markTestSkipped('Was not able to trigger rate limiter on API');
    }

    /**
     * @depends testIsAbleToHitRateLimiterWhenUnhandled
     */
    public function testDoesNotHitRateLimiterWhenHandled()
    {
        $testLength = date_create('+5 seconds');
        while (date_create() < $testLength) {
            $this->rateLimitHandledClient->getCheckoutInfo();
        }
    }
}
