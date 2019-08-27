<?php

namespace Univapay\Resources\Mixins;

use Univapay\Enums\CursorDirection;
use Univapay\Resources\Refund;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\OptionsValidator;
use Univapay\Utility\RequesterUtils;

trait GetRefunds
{
    use OptionsValidator;
    
    protected abstract function getRefundContext();

    public function listRefunds(
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
            Refund::class,
            $this->getRefundContext(),
            $query
        );
    }

    /**
     * @param array $opts See listRefunds parameters for valid opts keys
     */
    public function listRefundsByOptions(array $opts = [])
    {
        $rules = [
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(
            Refund::class,
            $this->getRefundContext(),
            $query
        );
    }
}
