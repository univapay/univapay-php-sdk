<?php

namespace Univapay\Resources\PaymentMethod;

use JsonSerializable;

class PaymentMethodPatch implements JsonSerializable
{
    private $email;
    public $metadata;

    public function __construct($email = null, array $metadata = null)
    {
        $this->email = $email;
        $this->metadata = $metadata;
    }

    public function jsonSerialize()
    {
        $values = [];
        if (isset($this->email)) {
            $values['email'] = $this->email;
        }
        if (isset($this->metadata)) {
            $values['metadata'] = $this->metadata;
        }
        return $values;
    }
}
