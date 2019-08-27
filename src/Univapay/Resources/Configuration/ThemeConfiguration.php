<?php

namespace Univapay\Resources\Configuration;

use Univapay\Resources\Jsonable;
use Univapay\Utility\FunctionalUtils as fp;
use Univapay\Utility\Json\JsonSchema;

class ThemeConfiguration
{
    use Jsonable;
    public $colors;

    public function __construct($colors)
    {
        $this->colors = $colors;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('colors', true, ColorsConfiguration::getSchema()->getparser());
    }
}
