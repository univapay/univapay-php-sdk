<?php
namespace UnivapayTest\Integration;

use Univapay\Enums\BankAccountStatus;
use Univapay\Enums\BankAccountType;
use Univapay\Resources\BankAccount;
use Univapay\Resources\Paginated;
use Money\Currency;
use PHPUnit\Framework\TestCase;

class BankAccountsTest extends TestCase
{
    use IntegrationSuite;
    
    public function testGetBankAccount()
    {
        $str = <<<EOD
        {
          "id": "11111111-1111-1111-1111-111111111111",
          "holder_name": "Test holder",
          "bank_name": "Test bank",
          "branch_name": "Test branch",
          "country": "JP",
          "bank_address": null,
          "currency": "JPY",
          "account_number": "XXXXXXX890",
          "last_four": "7890",
          "status": "new",
          "account_type": "checking",
          "created_on": "2017-03-28T05:37:06.850707Z",
          "primary": true
        }
EOD;
        $json = json_decode($str, $assoc = true);
        $bankAccount = BankAccount::getSchema()->parse($json, [$this->getClient()->getStoreBasedContext()]);
        $this->assertEquals('11111111-1111-1111-1111-111111111111', $bankAccount->id);
        $this->assertEquals('Test holder', $bankAccount->holderName);
        $this->assertEquals('Test bank', $bankAccount->bankName);
        $this->assertEquals('Test branch', $bankAccount->branchName);
        $this->assertEquals('JP', $bankAccount->country);
        $this->assertEquals(new Currency('JPY'), $bankAccount->currency);
        $this->assertEquals('XXXXXXX890', $bankAccount->accountNumber);
        $this->assertEquals('7890', $bankAccount->lastFour);
        $this->assertEquals(BankAccountStatus::NEW_ACCOUNT(), $bankAccount->status);
        $this->assertEquals(BankAccountType::CHECKING(), $bankAccount->accountType);
        $this->assertEquals(date_create('2017-03-28T05:37:06.850707Z'), $bankAccount->createdOn);
        $this->assertEquals(true, $bankAccount->primary);
    }

    public function testListBankAccounts()
    {
        $str = <<<EOD
        {
          "items": [
            {
              "id": "11111111-1111-1111-1111-111111111111",
              "holder_name": "Holder 1",
              "bank_name": "Bank 1",
              "branch_name": "Branch 1",
              "country": "JP",
              "bank_address": null,
              "currency": "JPY",
              "account_number": "123456",
              "last_four": "7890",
              "active": true,
              "status": "new",
              "account_type": "savings",
              "created_on": "2017-06-27T09:37:34.271388Z",
              "updated_on": "2017-07-11T05:59:37.396983Z",
              "primary": true
            },
            {
              "id": "22222222-2222-2222-2222-222222222222",
              "holder_name": "Holder 2",
              "bank_name": "Bank 2",
              "branch_name": "Branch 2",
              "country": "JP",
              "bank_address": null,
              "currency": "JPY",
              "account_number": "654321",
              "last_four": "1321",
              "active": true,
              "status": "new",
              "account_type": "checking",
              "created_on": "2017-06-27T07:43:30.349696Z",
              "updated_on": "2017-07-03T09:40:47.08313Z",
              "primary": false
            }
          ],
          "has_more": false
        }
EOD;
        $json = json_decode($str, true);
        $bankAccounts = Paginated::fromResponse(
            $json,
            [],
            BankAccount::class,
            $this->getClient()->getStoreBasedContext()
        );
        $this->assertEquals(false, $bankAccounts->hasMore);
        $this->assertEquals(2, count($bankAccounts->items));
        $this->assertEquals('Holder 2', $bankAccounts->items[1]->holderName);
    }
}
