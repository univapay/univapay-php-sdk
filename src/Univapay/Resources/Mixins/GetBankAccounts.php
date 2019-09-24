<?php

namespace Univapay\Resources\Mixins;

use Univapay\Enums\CursorDirection;
use Univapay\Resources\BankAccount;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\OptionsValidator;
use Univapay\Utility\RequesterUtils;

trait GetBankAccounts
{
    use OptionsValidator;
    
    abstract protected function getBankAccountContext();

    public function listBankAccounts(
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls([
            'cursor' => $cursor,
            'limit' => $limit,
            'cursor_direction' => $cursorDirection == null ? $cursorDirection : $cursorDirection->getValue()
        ]);
        return RequesterUtils::executeGetPaginated(
            BankAccount::class,
            $this->getBankAccountContext(),
            $query
        );
    }

    /**
     * @param array $opts See listBankAccounts parameters for valid opts keys
     */
    public function listBankAccountContextsByOptions(array $opts = [])
    {
        $rules = [
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(
            BankAccount::class,
            $this->getBankAccountContext(),
            $query
        );
    }
}
