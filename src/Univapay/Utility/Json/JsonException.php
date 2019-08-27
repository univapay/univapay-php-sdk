<?php

namespace Univapay\Utility\Json;

use Exception;

abstract class JsonException extends Exception
{

    public $path;

    public function __construct($path)
    {
        parent::__construct("Error at path $path");
        $this->path = $path;
    }
}
