<?php

namespace Univapay\Utility\Json;

class SchemaComponent
{
    public $path;
    public $required;
    public $formatter;

    public function __construct($path, $required, $formatter)
    {
        $this->path = trim($path, '/');
        $this->required = $required;
        $this->formatter = $formatter;
    }
}
