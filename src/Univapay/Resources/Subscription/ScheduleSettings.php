<?php

namespace Univapay\Resources\Subscription;

use DateInterval;
use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use JsonSerializable;
use Univapay\Enums\Field;
use Univapay\Enums\Reason;
use Univapay\Errors\UnivapayValidationError;
use Univapay\Resources\Jsonable;
use Univapay\Utility\DateUtils;
use Univapay\Utility\Json\JsonSchema;
use Univapay\Utility\FormatterUtils;
use Univapay\Utility\FunctionalUtils;

class ScheduleSettings implements JsonSerializable
{
    use Jsonable;
    
    public $startOn;
    public $zoneId;
    public $preserveEndOfMonth;
    public $retryInterval;

    public function __construct(
        DateTime $startOn = null,
        DateTimeZone $zoneId = null,
        $preserveEndOfMonth = false,
        DateInterval $retryInterval = null
    ) {
        if (isset($startOn, $zoneId)) {
            $startOn->setTimezone($zoneId);
        }

        $this->startOn = $startOn;
        $this->zoneId = $zoneId;
        $this->preserveEndOfMonth = $preserveEndOfMonth;
        $this->retryInterval = $retryInterval;
    }

    public function jsonSerialize() : array
    {
        if (isset($this->startOn) && $this->startOn < date_create()) {
            throw new UnivapayValidationError(Field::START_ON(), Reason::MUST_BE_FUTURE_TIME());
        }

        return FunctionalUtils::stripNulls([
            'start_on' => isset($this->startOn) ? $this->startOn->format('Y-m-d') : null,
            'zone_id' => isset($this->zoneId) ? $this->zoneId->getName() : null,
            'preserve_end_of_month' => $this->preserveEndOfMonth === true ? true : null,
            'retry_interval' => isset($this->retryInterval) ? DateUtils::asPeriodString($this->retryInterval) : null,
        ]);
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('start_on', false, FormatterUtils::of('getDateTime'))
            ->upsert('zone_id', true, FormatterUtils::of('getDateTimeZone'))
            ->upsert('retry_interval', false, FormatterUtils::of('getDateInterval'));
    }
}
