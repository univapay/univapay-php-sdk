<?php

namespace Univapay\Resources\Mixins;

use DateTime;
use Traversable;
use Univapay\Enums\AppTokenMode;
use Univapay\Enums\CursorDirection;
use Univapay\Resources\Charge;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\OptionsValidator;
use Univapay\Utility\RequesterUtils;
use Money\Currency;

trait GetCharges
{
    use OptionsValidator;

    abstract protected function getChargeContext();

    public function listCharges(
        $lastFour = null,
        $name = null,
        $expMonth = null,
        $expYear = null,
        $cardNumber = null,
        DateTime $from = null,
        DateTime $to = null,
        $email = null,
        $phone = null,
        $amountFrom = null,
        $amountTo = null,
        Currency $currency = null,
        $metadata = null,
        AppTokenMode $mode = null,
        $transactionTokenId = null,
        $gatewayCredentialsId = null,
        $gatewayTransactionId = null,
        $cursor = null,
        $limit = null,
        CursorDirection $cursorDirection = null
    ) {
        $query = FunctionalUtils::stripNulls([
            'last_four' => $lastFour,
            'name' => $name,
            'exp_month' => $expMonth,
            'exp_year' => $expYear,
            'card_number' => $cardNumber,
            'from' => isset($from) ? $from->format(DateTime::ATOM) : null,
            'to' => isset($to) ? $to->format(DateTime::ATOM) : null,
            'email' => $email,
            'phone' => $phone,
            'amount_from' => $amountFrom,
            'amount_to' => $amountTo,
            'currency' => isset($currency) ? $currency->getCode() : null,
            'metadata' => $metadata,
            'mode' => isset($mode) ? $mode->getValue() : null,
            'transaction_token_id' => $transactionTokenId,
            'cursor' => $cursor,
            'limit' => $limit,
            'cursor_direction' => isset($cursorDirection) ? $cursorDirection->getValue() : null
        ]);
        return RequesterUtils::executeGetPaginated(Charge::class, $this->getChargeContext(), $query);
    }

    /**
     * @param array $opts See listCharges parameters for valid opts keys
     */
    public function listChargesByOptions(array $opts = [])
    {
        $rules = [
            'from' => 'ValidationHelper::getAtomDate',
            'to' => 'ValidationHelper::getAtomDate',
            'currency' => 'ValidationHelper::getEnumValue',
            'mode' => 'ValidationHelper::getEnumValue',
            'cursor_direction' => 'ValidationHelper::getEnumValue',
        ];

        $query = $this->validate(FunctionalUtils::stripNulls($opts), $rules);
        return RequesterUtils::executeGetPaginated(Charge::class, $this->getChargeContext(), $query);
    }
}
