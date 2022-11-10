<?php

namespace Univapay\Resources;

use Univapay\Utility\RequesterUtils;

trait Pollable
{
    abstract protected function getIdContext();

    abstract protected function pollableStatuses();

    public function awaitResult($retry = 0)
    {
        $idContext = $this->getIdContext();
        $response = RequesterUtils::executeGet(self::class, $idContext, ['polling' => 'true']);
        $retryCount = 0;
        while ($retryCount < $retry && in_array($response->status, $this->pollableStatuses())) {
            $retryCount++;
            $response = RequesterUtils::executeGet(self::class, $idContext, ['polling' => 'true']);
        }
        return $response;
    }
}
