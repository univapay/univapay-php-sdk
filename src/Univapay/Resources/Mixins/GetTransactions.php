<?php

namespace Univapay\Resources\Mixins;

use DateTime;
use Univapay\Enums\AppTokenMode;
use Univapay\Enums\CursorDirection;
use Univapay\Enums\ChargeStatus;
use Univapay\Resources\Paginated;
use Univapay\Resources\Transaction;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\OptionsValidator;
use Univapay\Utility\RequesterUtils;

trait GetTransactions
{
    use OptionsValidator;

    abstract protected function getTransactionContext();
    
    public function listTransactions(
        DateTime $from = null,
        DateTime $to = null,
        ChargeStatus $status = null,
        TransactionType $type = null,
        $search = null,
        AppTokenMode $mode = null,
        $gatewayCredentialsId = null,
        $gatewayTransactionId = null,
        $metadata = null,
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls([
            'from' => $from->getTimestamp() * 1000,
            'to' => $to->getTimestamp() * 1000,
            'status' => isset($status) ? $status->getValue() : null,
            'type' => isset($type) ? $type->getValue() : null,
            'search' => $search,
            'mode' => isset($mode) ? $mode->getValue() : null,
            'metadata' => $metadata,
            'cursor' => $cursor,
            'limit' => $limit,
            'cursor_direction' => isset($cursorDirection) ? $cursorDirection.getValue() : null
        ]);
        
        return RequesterUtils::executeGetPaginated(Transaction::class, $this->getTransactionContext(), $query);
    }

    /**
     * @param array $opts See listTransactions parameters for valid opts keys
     */
    public function listTransactionsByOptions(array $opts = [])
    {
        $rules = [
            'from' => 'ValidationHelper::getAtomDate',
            'to' => 'ValidationHelper::getAtomDate',
            'status' => 'ValidationHelper::getEnumValue',
            'type' => 'ValidationHelper::getEnumValue',
            'mode' => 'ValidationHelper::getEnumValue',
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(Transaction::class, $this->getTransactionContext(), $query);
    }
}
