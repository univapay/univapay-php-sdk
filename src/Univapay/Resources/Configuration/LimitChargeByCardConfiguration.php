<?php

namespace Univapay\Resources\Configuration;

use \Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class LimitChargeByCardConfiguration
{
    
    use Jsonable;
    
    public $quantityOfCharges;
    public $durationWindow;
    
    public function __construct($quantityOfCharges, $durationWindow)
    {
        $this->quantityOfCharges = $quantityOfCharges;
        $this->durationWindow = $durationWindow;
    }
    
    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
