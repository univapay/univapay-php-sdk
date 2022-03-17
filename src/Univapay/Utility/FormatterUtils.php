<?php

namespace Univapay\Utility;

use DateInterval;
use DateTimeZone;
use Money\Currency;
use Money\Money;
use Univapay\Enums\InstallmentPlanType;

class FormatterUtils
{
    public static function of($functionName)
    {
        return self::class . "::$functionName";
    }

    public static function getDateTime($dateTime)
    {
        return date_create($dateTime);
    }

    public static function getDateTimeZone($dateTimeZone)
    {
        return new DateTimeZone($dateTimeZone);
    }

    public static function getDateInterval($dateInterval)
    {
        return new DateInterval($dateInterval);
    }

    public static function getTypedEnum($typedEnumClass)
    {
        return function ($value) use ($typedEnumClass) {
            return call_user_func([$typedEnumClass, 'fromValue'], $value);
        };
    }

    // https://stackoverflow.com/a/42598056/6549664
    public static function formatDateIntervalISO(DateInterval $dateInterval)
    {
        list($date, $time) = explode('T', $dateInterval->format('P%yY%mM%dDT%hH%iM%sS'));
        $res =
            str_replace(['M0D', 'Y0M', 'P0Y'], ['M', 'Y', 'P'], $date) .
            rtrim(str_replace(['M0S', 'H0M', 'T0H'], ['M', 'H', 'T'], "T$time"), 'T');
        if ($res == 'P') {
            return 'PT0S';
        }
        return $res;
    }

    public static function getCurrency($currency)
    {
        return new Currency($currency);
    }
    
    public static function getMoney($currencyKey, $currencyAtRoot = false)
    {
        return function ($value, $json, $parent) use ($currencyKey, $currencyAtRoot) {
            $currencyValue = $currencyAtRoot ? $parent[$currencyKey] : $json[$currencyKey];
            return new Money($value, new Currency($currencyValue));
        };
    }
    
    public static function getListOf($schemaParser = 'Univapay\Utility\FunctionalUtils::identity')
    {
        return function (array $values, $json = null, $parent = null) use ($schemaParser) {
            $curriedParser = function ($value) use ($schemaParser, $json, $parent) {
                // Pass the item as the contextRoot instead of the whole paginated json as that is expected
                return call_user_func($schemaParser, $value, $value);
            };
            return array_map($curriedParser, $values);
        };
    }
}
