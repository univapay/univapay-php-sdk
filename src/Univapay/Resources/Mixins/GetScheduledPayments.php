<?php

namespace Univapay\Resources\Mixins;

use Univapay\Enums\CursorDirection;
use Univapay\Resources\ScheduledPayment;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\OptionsValidator;
use Univapay\Utility\RequesterUtils;

trait GetScheduledPayments
{
    use OptionsValidator;
    
    protected abstract function getScheduledPaymentContext();

    public function listScheduledPayments(
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls([
            'cursor' => $cursor,
            'limit' => $limit,
            'cursor_direction' => isset($cursorDirection) ? $cursorDirection->getValue() : null
        ]);

        return RequesterUtils::executeGetPaginated(
            ScheduledPayment::class,
            $this->getScheduledPaymentContext(),
            $query
        );
    }

    /**
     * @param array $opts See listScheduledPayments parameters for valid opts keys
     */
    public function listScheduledPaymentsByOptions(array $opts = [])
    {
        $rules = [
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(
            ScheduledPayment::class,
            $this->getScheduledPaymentContext(),
            $query
        );
    }
}
