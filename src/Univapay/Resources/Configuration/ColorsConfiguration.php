<?php

namespace Univapay\Resources\Configuration;

use Univapay\Resources\Jsonable;
use Univapay\Utility\FunctionalUtils as fp;
use Univapay\Utility\Json\JsonSchema;

class ColorsConfiguration
{
    use Jsonable;
    public $mainBackground;
    public $secondaryBackground;
    public $mainColor;
    public $mainText;
    public $primaryText;
    public $secondaryText;
    public $baseText;

    public function __construct(
        $mainBackground,
        $secondaryBackground,
        $mainColor,
        $mainText,
        $primaryText,
        $secondaryText,
        $baseText
    ) {
        $this->mainBackground = $mainBackground;
        $this->secondaryBackground = $secondaryBackground;
        $this->mainColor = $mainColor;
        $this->mainText = $mainText;
        $this->primaryText = $primaryText;
        $this->secondaryText = $secondaryText;
        $this->baseText = $baseText;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
