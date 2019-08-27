<?php

namespace Univapay\Resources;

use Univapay\Enums\AppTokenMode;
use Univapay\Enums\CancelStatus;
use Univapay\Utility\Json\JsonSchema;

class SimpleList
{
    public $items;
    private $jsonableClass;
    private $context;

    public function __construct($items, $jsonableClass, $context)
    {
        $this->items = $items;
        $this->jsonableClass = $jsonableClass;
        $this->context = $context;
    }

    public static function fromResponse(
        $response,
        $jsonableClass,
        $context
    ) {
        $parser = $jsonableClass::getContextParser($context);
        return new SimpleList(
            array_map($parser, $response),
            $jsonableClass,
            $context
        );
    }
}
