<?php

namespace Univapay\Resources\Mixins;

use Univapay\Enums\CursorDirection;
use Univapay\Resources\Store;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\OptionsValidator;
use Univapay\Utility\RequesterUtils;

trait GetStores
{
    use OptionsValidator;
    
    abstract protected function getStoreContext();

    public function listStores(
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
            Store::class,
            $this->getCancelContext(),
            $query
        );
    }

    /**
     * @param array $opts See listStores parameters for valid opts keys
     */
    public function listStoresByOptions(array $opts = [])
    {
        $rules = [
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(
            Store::class,
            $this->getStoreContext(),
            $query
        );
    }
}
