<?php

namespace Univapay\Resources\Mixins;

use Univapay\Enums\CursorDirection;
use Univapay\Resources\Ledger;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\OptionsValidator;
use Univapay\Utility\RequesterUtils;

trait GetLedgers
{
    use OptionsValidator;
    
    abstract protected function getLedgerContext();

    public function listLedgers(
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
            Ledger::class,
            $this->getLedgerContext(),
            $query
        );
    }

    /**
     * @param array $opts See listLedgers parameters for valid opts keys
     */
    public function listLedgersByOptions(array $opts = [])
    {
        $rules = [
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(
            Ledger::class,
            $this->getLedgerContext(),
            $query
        );
    }
}
