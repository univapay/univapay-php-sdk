<?php
namespace UnivapayTest\Integration;

use Univapay\Enums\TransferStatus;
use Univapay\Resources\Transfer;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class TransferTest extends TestCase
{
    use IntegrationSuite;

    public function testTransferParse()
    {
        $str = <<<EOD
        {
          "id": "11111111-1111-1111-1111-11111111111",
          "bank_account_id": "22222222-2222-2222-2222-222222222222",
          "amount": 0,
          "currency": "JPY",
          "amount_formatted": 0,
          "status": "blank",
          "error_code": null,
          "error_text": null,
          "metadata": {
             "key": "value"
          },
          "note": "a note",
          "from": "2017-10-07",
          "to": "2017-10-14",
          "created_on": "2017-10-14T08:00:00.664568Z"
        }
EOD;

        $json = json_decode($str, true);
        $transfer = Transfer::getSchema()->parse($json, [$this->getClient()->getStoreBasedContext()]);
        $this->assertEquals('11111111-1111-1111-1111-11111111111', $transfer->id);
        $this->assertEquals('22222222-2222-2222-2222-222222222222', $transfer->bankAccountId);
        $this->assertEquals(Money::JPY(0), $transfer->amount);
        $this->assertEquals(new Currency('JPY'), $transfer->currency);
        $this->assertEquals(0, $transfer->amountFormatted);
        $this->assertEquals(TransferStatus::BLANK(), $transfer->status);
        $this->assertNull($transfer->errorCode);
        $this->assertNull($transfer->errorText);
        $this->assertEquals(['key' => 'value'], $transfer->metadata);
        $this->assertEquals($transfer->note, 'a note');
        $this->assertEquals(date_create('2017-10-07'), $transfer->from);
        $this->assertEquals(date_create('2017-10-14'), $transfer->to);
        $this->assertEquals(date_create('2017-10-14T08:00:00.664568Z'), $transfer->createdOn);
    }
}
