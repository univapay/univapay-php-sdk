<?php

namespace Univapay\Resources\PaymentMethod;

use JsonSerializable;
use Univapay\Enums\CallMethod;
use Univapay\Enums\Field;
use Univapay\Enums\OnlineBrand;
use Univapay\Enums\OsType;
use Univapay\Enums\PaymentType;
use Univapay\Enums\Reason;
use Univapay\Enums\TokenType;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Utility\FunctionalUtils;

class OnlinePayment extends PaymentMethod implements JsonSerializable
{
    private $brand;
    private $callMethod;
    private $userIdentifier;
    private $osType;

    public function __construct(
        $email,
        OnlineBrand $brand,
        array $metadata = null,
        $ipAddress = null,
        CallMethod $callMethod = null,
        $userIdentifier = null,
        OsType $osType = null
    ) {
        parent::__construct(PaymentType::ONLINE(), null, $email, $ipAddress, null, $metadata);
        $this->brand = $brand;
        $this->callMethod = $callMethod;
        $this->userIdentifier = $userIdentifier;
        $this->osType = $osType;
    }

    // Does not take in a token type
    protected function acceptsTokenType(TokenType $tokenType = null)
    {
    }

    public function jsonSerialize()
    {
        $parentData = parent::jsonSerialize();
        $parentData['data'] = FunctionalUtils::stripNulls([
            'brand' => $this->brand->getName(),
            'call_method' => isset($this->callMethod)
                ? $this->callMethod->getName()
                : null,
            'user_identifier' => isset($this->userIdentifier)
                ? $this->userIdentifier
                : null,
            'os_type' => isset($this->osType)
                ? $this->osType->getName()
                : null
        ]);

        return $parentData;
    }
}
