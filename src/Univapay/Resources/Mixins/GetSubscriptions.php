<?php

namespace Univapay\Resources\Mixins;

use Univapay\Enums\AppTokenMode;
use Univapay\Enums\CursorDirection;
use Univapay\Enums\SubscriptionStatus;
use Univapay\Resources\Subscription;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\OptionsValidator;
use Univapay\Utility\RequesterUtils;

trait GetSubscriptions
{
    use OptionsValidator;
    
    protected abstract function getSubscriptionContext();

    public function listSubscriptions(
        $search = null,
        SubscriptionStatus $status = null,
        AppTokenMode $mode = null,
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls([
            'search' => $search,
            'status' => isset($status) ? $status->getValue() : null,
            'mode' => isset($mode) ? $mode->getValue() : null,
            'cursor' => $cursor,
            'limit' => $limit,
            'cursor_direction' => isset($cursorDirection) ? $cursorDirection->getValue() : null
        ]);

        return RequesterUtils::executeGetPaginated(
            Subscription::class,
            $this->getSubscriptionContext(),
            $query
        );
    }

    /**
     * @param array $opts See listSubscriptions parameters for valid opts keys
     */
    public function listSubscriptionsByOptions(array $opts = [])
    {
        $rules = [
            'status' => 'ValidationHelper::getEnumValue',
            'type' => 'ValidationHelper::getEnumValue',
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(
            Subscription::class,
            $this->getSubscriptionContext(),
            $query
        );
    }
}
