<?php

namespace Univapay\Utility;

use DateInterval;

abstract class DateUtils
{

    public static function asPeriodString(DateInterval $date)
    {
        $interval = 'P' .
            ($date->y == 0 ? null : $date->y . 'Y') .
            ($date->m == 0 ? null : $date->m . 'M') .
            ($date->d == 0 ? null : $date->d . 'D');
        $timeInterval =
            ($date->h == 0 ? null : $date->h . 'H') .
            ($date->i == 0 ? null : $date->i . 'M') .
            ($date->s == 0 ? null : $date->y . 'S');
        return $interval . ((!empty($timeInterval)) ? 'T' . $timeInterval : null);
    }
}
