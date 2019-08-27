<?php
namespace UnivapayTest\Integration;

use DateTime;
use PHPUnit\Framework\TestCase;

class MerchantTest extends TestCase
{
    use IntegrationSuite;

    public function testGetMe()
    {
        $me = $this->getClient()->getMe();
        $this->assertLessThan(date_create('now'), $me->createdOn);
        $this->assertTrue(is_string($me->name));
    }
}
