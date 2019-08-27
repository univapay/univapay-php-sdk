<?php

namespace Univapay\Resources;

use Univapay\Utility\RequesterUtils;

abstract class Resource
{
    public $id;
    protected $context;

    protected function __construct($id, $context)
    {
        $this->id = $id;
        $this->context = $context;
    }

    protected function getIdContext()
    {
        if (strpos($this->context->getFullURL(), $this->id)) {
            return $this->context;
        } else {
            return $this->context->appendPath($this->id);
        }
    }

    public function fetch()
    {
        $context = $this->getIdContext();
        return RequesterUtils::executeGet(get_class($this), $context, []);
    }

    public function update($updates)
    {
        $context = $this->getIdContext();
        return RequesterUtils::executePatch(get_class($this), $context, $updates);
    }
}
