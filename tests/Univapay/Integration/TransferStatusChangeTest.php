<?php
namespace UnivapayTest\Integration;

use DateTime;
use Univapay\Enums\TransferStatus;
use Univapay\Resources\TransferStatusChange;
use PHPUnit\Framework\TestCase;

class TransferStatusChangeTest extends TestCase
{
    use IntegrationSuite;

    public function testTransferStatusChangeParse()
    {
        $str = <<<EOD
        {
            "id": "11111111-1111-1111-1111-111111111111",
            "merchant_id": "22222222-2222-2222-2222-222222222222",
            "transfer_id": "33333333-3333-3333-3333-333333333333",
            "old_status": "processing",
            "new_status": "failed",
            "reason": "a reason",
            "created_on": "2017-10-26T17:37:33.742404+09:00"
        }
EOD;

        $json = json_decode($str, true);
        $transferStatusChange = TransferStatusChange::getSchema()->parse(
            $json,
            [$this->getClient()->getStoreBasedContext()]
        );
        $this->assertEquals('11111111-1111-1111-1111-111111111111', $transferStatusChange->id);
        $this->assertEquals('22222222-2222-2222-2222-222222222222', $transferStatusChange->merchantId);
        $this->assertEquals('33333333-3333-3333-3333-333333333333', $transferStatusChange->transferId);
        $this->assertEquals(TransferStatus::PROCESSING(), $transferStatusChange->oldStatus);
        $this->assertEquals(TransferStatus::FAILED(), $transferStatusChange->newStatus);
        $this->assertEquals('a reason', $transferStatusChange->reason);
        $this->assertEquals(date_create('2017-10-26T17:37:33.742404+09:00'), $transferStatusChange->createdOn);
    }
}
