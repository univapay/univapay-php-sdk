<?php

namespace Univapay\Resources\Configuration;

use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class UserTransactionsConfiguration
{
    use Jsonable;
    
    public $enabled;
    public $notifyCustomer;
    
    public function __construct($enabled, $notifyCustomer)
    {
        $this->enabled = $enabled;
        $this->notifyCustomer = $notifyCustomer;
    }
    
    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class);
    }
}
