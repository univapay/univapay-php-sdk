<?php

namespace Univapay\Tests\Unit\Resources;

use PHPUnit\Framework\TestCase;
use Univapay\Errors\UnivapayLogicError;
use Univapay\Resources\ThreeDSMPI;

class ThreeDSMPITest extends TestCase
{
    public function testValidThreeDSMPI()
    {
        $threeDSMPI = new ThreeDSMPI(
            '1234567890123456789012345678',
            '12',
            '058e4f09-37c7-47e5-9d24-47e8ffa77442',
            '7307b449-375a-4297-94d9-81314d4371c2',
            '2.1.0',
            'Y'
        );

        $this->assertEquals('1234567890123456789012345678', $threeDSMPI->authenticationValue);
        $this->assertEquals('12', $threeDSMPI->eci);
        $this->assertEquals('058e4f09-37c7-47e5-9d24-47e8ffa77442', $threeDSMPI->dsTransactionId);
        $this->assertEquals('7307b449-375a-4297-94d9-81314d4371c2', $threeDSMPI->serverTransactionId);
        $this->assertEquals('2.1.0', $threeDSMPI->messageVersion);
        $this->assertEquals('Y', $threeDSMPI->transactionStatus);
    }

    public function testInvalidThreeDSMPI()
    {
        $this->expectException(UnivapayLogicError::class);

        new ThreeDSMPI(
            '1234567890123456789012345678',
            '12',
            null,
            '7307b449-375a-4297-94d9-81314d4371c2',
            '2.1.0',
            'Y'
        );
    }
}
