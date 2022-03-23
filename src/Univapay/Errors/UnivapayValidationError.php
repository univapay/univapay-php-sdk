<?php

namespace Univapay\Errors;

use Univapay\Enums\Field;
use Univapay\Enums\Reason;

class UnivapayValidationError extends UnivapayRequestError
{
    public function __construct(Field $field, Reason $reason)
    {
        parent::__construct('preflight', 'error', 'VALIDATION_ERROR', [
            'field' => $field->getValue(),
            'reason' => $reason->getValue()
        ]);
    }

    public function addError(Field $field, Reason $reason)
    {
        parent::$errors[] = [
            'field' => $field->getValue(),
            'reason' => $reason->getValue()
        ];
        return $this;
    }
}
