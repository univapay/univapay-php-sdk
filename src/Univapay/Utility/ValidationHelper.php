<?php

namespace Univapay\Utility;

use DateTime;
use Univapay\Enums\TypedEnum;

class ValidationHelper
{
    public static function isArray(array $array)
    {
        return $array;
    }

    public static function getAtomDate(DateTime $date)
    {
        return $date->format(DateTime::ATOM);
    }

    public static function getEnumValue(TypedEnum $enum)
    {
        return $enum->getValue();
    }
}
