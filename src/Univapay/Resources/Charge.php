<?php

namespace Univapay\Resources;

use DateTime;
use Money\Currency;
use Money\Money;
use Univapay\Enums\AppTokenMode;
use Univapay\Enums\ChargeStatus;
use Univapay\Enums\ChargeType;
use Univapay\Enums\Field;
use Univapay\Enums\Reason;
use Univapay\Enums\RefundReason;
use Univapay\Enums\TokenType;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Resources\PaymentToken\QrMerchantToken;
use Univapay\Resources\PaymentToken\OnlineToken;
use Univapay\Resources\Mixins\GetCancels;
use Univapay\Resources\Mixins\GetRefunds;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\FunctionalUtils;
use Univapay\Utility\RequesterUtils;
use Univapay\Utility\Json\JsonSchema;

class Charge extends Resource
{
    use Jsonable;
    use Pollable;
    use GetCancels, GetRefunds {
        GetCancels::validate insteadof GetRefunds;
    }

    public $storeId;
    public $transactionTokenId;
    public $transactionTokenType;
    public $subscriptionId;
    public $requestedCurrency;
    public $requestedAmount;
    public $requestedAmountFormatted;
    public $status;
    public $mode;
    public $createdOn;
    public $chargedCurrency;
    public $chargedAmount;
    public $chargedAmountFormatted;
    public $onlyDirectCurrency;
    public $captureAt;
    public $error;
    public $metadata;

    public function __construct(
        $id,
        $storeId,
        $transactionTokenId,
        TokenType $transactionTokenType,
        $subscriptionId,
        Currency $requestedCurrency,
        Money $requestedAmount,
        $requestedAmountFormatted,
        ChargeStatus $status,
        AppTokenMode $mode,
        DateTime $createdOn,
        Currency $chargedCurrency = null,
        Money $chargedAmount = null,
        $chargedAmountFormatted = null,
        $onlyDirectCurrency = null,
        DateTime $captureAt = null,
        $error = null,
        $metadata = null,
        $context = null
    ) {
        parent::__construct($id, $context);
        $this->storeId = $storeId;
        $this->transactionTokenId = $transactionTokenId;
        $this->transactionTokenType = $transactionTokenType;
        $this->subscriptionId = $subscriptionId;
        $this->requestedCurrency = $requestedCurrency;
        $this->requestedAmount = $requestedAmount;
        $this->requestedAmountFormatted = $requestedAmountFormatted;
        $this->chargedCurrency = $chargedCurrency;
        $this->chargedAmount = $chargedAmount;
        $this->chargedAmountFormatted = $chargedAmountFormatted;
        $this->onlyDirectCurrency = $onlyDirectCurrency;
        $this->captureAt = $captureAt;
        $this->status = $status;
        $this->error = $error;
        $this->metadata = $metadata;
        $this->mode = $mode;
        $this->createdOn = $createdOn;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('transaction_token_type', true, FormatterUtils::getTypedEnum(TokenType::class))
            ->upsert('requested_currency', true, FormatterUtils::of('getCurrency'))
            ->upsert('requested_amount', true, FormatterUtils::getMoney('requested_currency'))
            ->upsert('charged_currency', false, FormatterUtils::of('getCurrency'))
            ->upsert('charged_amount', false, FormatterUtils::getMoney('charged_currency'))
            ->upsert('capture_at', false, FormatterUtils::of('getDateTime'))
            ->upsert('status', true, FormatterUtils::getTypedEnum(ChargeStatus::class))
            ->upsert('mode', true, FormatterUtils::getTypedEnum(AppTokenMode::class))
            ->upsert('created_on', true, FormatterUtils::of('getDateTime'));
    }

    protected function getIdContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'charges', $this->id]);
    }

    protected function pollableStatuses()
    {
        return [
            (string) ChargeStatus::PENDING() => array_diff(ChargeStatus::findValues(), [ChargeStatus::PENDING()]),
            (string) ChargeStatus::AUTHORIZED() => [
                ChargeStatus::SUCCESSFUL(), ChargeStatus::FAILED(), ChargeStatus::ERROR(), ChargeStatus::CANCELED()
            ],
            (string) ChargeStatus::AWAITING() => [
                ChargeStatus::SUCCESSFUL(), ChargeStatus::FAILED(), ChargeStatus::ERROR(), ChargeStatus::CANCELED()
            ]
        ];
    }

    public function patch(array $metadata)
    {
        return RequesterUtils::executePatch(self::class, $this->getIdContext(), ['metadata' => $metadata]);
    }

    public function createRefund(
        Money $money,
        RefundReason $reason = null,
        $message = null,
        array $metadata = null
    ) {
        if (isset($reason) && RefundReason::CHARGEBACK() === $reason) {
            throw new UnivapayValidationError(Field::REASON(), Reason::INVALID_PERMISSIONS());
        }
        $payload = FunctionalUtils::stripNulls(
            $money->jsonSerialize() +
            [
                'reason' => isset($reason) ? $reason->getValue() : null,
                'message' => $message,
                'metadata' => $metadata
            ]
        );
        $context = $this->getRefundContext();
        return RequesterUtils::executePost(Refund::class, $context, $payload);
    }

    public function capture(Money $money = null)
    {
        $context = $this->getCaptureContext();
        return RequesterUtils::executePost(null, $context, $money);
    }

    public function cancel(array $metadata = null)
    {
        $payload = FunctionalUtils::stripNulls([
            'metadata' => $metadata
        ]);
        $context = $this->getCancelContext();
        return RequesterUtils::executePost(Cancel::class, $context, $payload);
    }

    public function qrMerchantToken()
    {
        $context = $this->getQrMerchantTokenContext();
        return RequesterUtils::executeGet(QrMerchantToken::class, $context);
    }

    public function onlineToken()
    {
        $context = $this->getOnlineTokenContext();
        return RequesterUtils::executeGet(OnlineToken::class, $context);
    }

    protected function getCaptureContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'charges', $this->id, 'capture']);
    }

    protected function getCancelContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'charges', $this->id, 'cancels']);
    }

    protected function getRefundContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'charges', $this->id, 'refunds']);
    }

    protected function getQrMerchantTokenContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'charges', $this->id, 'qr']);
    }

    protected function getOnlineTokenContext()
    {
        return $this->context->withPath(['stores', $this->storeId, 'charges', $this->id, 'issuerToken']);
    }
}
