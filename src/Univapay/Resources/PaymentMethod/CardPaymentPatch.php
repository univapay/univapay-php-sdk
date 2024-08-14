<?php

namespace Univapay\Resources\PaymentMethod;

use JsonSerializable;

class CardPaymentPatch extends PaymentMethodPatch implements JsonSerializable
{
    private $cvv;

    public function __construct($cvv, $email = null, array $metadata = null)
    {
        parent::__construct($email, $metadata);
        $this->cvv = $cvv;
    }

    public function jsonSerialize() : array
    {
        $values = parent::jsonSerialize();
        $values['data'] = ['cvv' => $this->cvv];
        return $values;
    }
}
