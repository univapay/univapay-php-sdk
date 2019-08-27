<?php
namespace UnivapayTest\Integration;

use Univapay\UnivapayClient;
use Univapay\UnivapayClientOptions;
use Univapay\Enums\AppTokenMode;
use Univapay\Resources\Authentication\AppJWT;
use Univapay\Resources\Authentication\StoreAppJWT;

trait IntegrationSuite
{
    use Requests;

    private $client = null;
    public $storeAppJWT;
    public $clientOptions;

    protected function init()
    {
        $token = getenv('UNIVAPAY_PHP_TEST_TOKEN');
        $secret = getenv('UNIVAPAY_PHP_TEST_SECRET');
        $this->clientOptions = new UnivapayClientOptions(getenv('UNIVAPAY_PHP_TEST_ENDPOINT'));
        $this->storeAppJWT = AppJWT::createToken($token, $secret);

        if ($this->storeAppJWT instanceof StoreAppJWT && $this->storeAppJWT->mode === AppTokenMode::TEST()) {
            $this->client = new UnivapayClient($this->storeAppJWT, $this->clientOptions);
        } else {
            $this->markTestSkipped('Unable to run test suite with a Merchant app token or a non-test token');
        }
    }

    public function getClient()
    {
        if ($this->client === null) {
            $this->init();
        }
        return $this->client;
    }
}
