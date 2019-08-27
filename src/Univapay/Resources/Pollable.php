<?php

namespace Univapay\Resources;

use Univapay\Utility\RequesterUtils;

trait Pollable
{
    abstract protected function getIdContext();

    public function awaitResult()
    {
        $idContext = $this->getIdContext();
        return RequesterUtils::executeGet(self::class, $idContext, ['polling' => 'true']);
    }
}
