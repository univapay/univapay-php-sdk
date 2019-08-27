<?php

namespace Univapay\Resources\Mixins;

use Univapay\Enums\ActiveFilter;
use Univapay\Enums\AppTokenMode;
use Univapay\Enums\CursorDirection;
use Univapay\Enums\Field;
use Univapay\Enums\Reason;
use Univapay\Enums\TokenType;
use Univapay\Enums\TransactionType;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Resources\TransactionToken;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\OptionsValidator;
use Univapay\Utility\RequesterUtils;

trait GetTransactionTokens
{
    use OptionsValidator;

    protected abstract function getTransactionTokenContext();

    public function listTransactionTokens(
        $search = null,
        $UnivapayCustomerId = null,
        TokenType $type = null,
        AppTokenMode $mode = null,
        ActiveFilter $active = null,
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        if (isset($type) && $type === TokenType::ONE_TIME()) {
            throw new UnivapayValidationError(Field::Type(), Reason::INVALID_TOKEN_TYPE());
        }

        $context = $this->getTransactionTokenContext();
        $query = FunctionalUtils::stripNulls([
            'search' => $search,
            'active' => isset($active) ? $active->getValue() : null,
            'customer_id' => $UnivapayCustomerId,
            'type' => isset($type) ? $type->getValue() : null,
            'mode' => isset($mode) ? $mode->getValue() : null,
            'cursor' => $cursor,
            'limit' => $limit,
            'cursor_direction' => isset($cursorDirection) ? $cursorDirection->getValue() : null
        ]);
        return RequesterUtils::executeGetPaginated(TransactionToken::class, $context, $query);
    }

    /**
     * @param array $opts See listTransactionTokens parameters for valid opts keys
     */
    public function listTransactionTokensByOptions(array $opts = [])
    {
        $rules = [
            'active' => 'ValidationHelper::getEnumValue',
            'status' => 'ValidationHelper::getEnumValue',
            'type' => 'ValidationHelper::getEnumValue',
            'mode' => 'ValidationHelper::getEnumValue',
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
